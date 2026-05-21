import { __ } from '@wordpress/i18n'
import { useState, useEffect, useRef, useCallback } from '@wordpress/element'

// URL hashes that mean "the visitor wants to see the comments".
const HASH_TRIGGERS = ['#comment', '#respond', '#llc-comments']

/**
 * Front end comments lazy loader.
 *
 * Fetches the server-rendered comments HTML from the REST API when the
 * visitor clicks the button or scrolls the comments area into view.
 *
 * @param {Object}  props             Component props (from `llcFrontend`).
 * @param {number}  props.postId      Post ID.
 * @param {string}  props.restUrl     Comments REST endpoint URL.
 * @param {string}  props.method      Load method: 'scroll' or 'click'.
 * @param {string}  props.buttonText  Load button label.
 * @param {string}  props.buttonClass Extra button CSS classes.
 * @param {boolean} props.showLoader  Whether to show the spinner.
 */
const CommentsLoader = ({
	postId,
	restUrl,
	method,
	buttonText,
	buttonClass,
	showLoader,
}) => {
	const [status, setStatus] = useState('idle')
	const [html, setHtml] = useState('')
	const containerRef = useRef(null)
	const requested = useRef(false)

	const load = useCallback(async () => {
		// Guard against duplicate requests (scroll + hash, etc.).
		if (requested.current) {
			return
		}
		requested.current = true
		setStatus('loading')

		try {
			const response = await fetch(
				`${restUrl}?post_id=${encodeURIComponent(postId)}`,
				{ headers: { Accept: 'application/json' } },
			)

			if (!response.ok) {
				throw new Error('Request failed')
			}

			const data = await response.json()
			setHtml(data.html || '')
			setStatus('loaded')
		} catch (e) {
			requested.current = false
			setStatus('error')
		}
	}, [postId, restUrl])

	// Load immediately if the URL points at the comments.
	useEffect(() => {
		const hash = window.location.hash

		if (hash && HASH_TRIGGERS.some((h) => hash.startsWith(h))) {
			load()
		}
	}, [load])

	// Scroll based loading using an IntersectionObserver.
	useEffect(() => {
		if (method !== 'scroll' || status !== 'idle') {
			return undefined
		}

		const el = containerRef.current

		if (!el) {
			return undefined
		}

		// No observer support: just load right away.
		if (!('IntersectionObserver' in window)) {
			load()
			return undefined
		}

		const observer = new IntersectionObserver(
			(entries) => {
				if (entries.some((entry) => entry.isIntersecting)) {
					observer.disconnect()
					load()
				}
			},
			{ rootMargin: '200px 0px' },
		)

		observer.observe(el)

		return () => observer.disconnect()
	}, [method, status, load])

	// Once injected, re-init WP comment reply and jump to the target.
	useEffect(() => {
		if (status !== 'loaded') {
			return
		}

		if (window.addComment && typeof window.addComment.init === 'function') {
			window.addComment.init()
		}

		const hash = window.location.hash

		if (hash && hash.startsWith('#comment')) {
			const target = document.getElementById(hash.slice(1))

			if (target) {
				target.scrollIntoView()
			}
		}
	}, [status])

	if (status === 'loaded') {
		return (
			// eslint-disable-next-line react/no-danger
			<div dangerouslySetInnerHTML={{ __html: html }} />
		)
	}

	return (
		<div ref={containerRef} className="llc-loader">
			{status === 'loading' && showLoader && (
				<div className="llc-spinner" aria-live="polite">
					<span className="llc-spinner-circle" aria-hidden="true" />
					<span className="screen-reader-text">
						{__('Loading comments…', 'lazy-load-for-comments')}
					</span>
				</div>
			)}

			{status === 'error' && (
				<p className="llc-error">
					{__(
						'Comments could not be loaded.',
						'lazy-load-for-comments',
					)}{' '}
					<button
						type="button"
						className="llc-retry"
						onClick={() => {
							requested.current = false
							setStatus('idle')
							load()
						}}
					>
						{__('Retry', 'lazy-load-for-comments')}
					</button>
				</p>
			)}

			{method === 'click' && status === 'idle' && (
				<button
					type="button"
					className={`llc-button ${buttonClass || ''}`.trim()}
					onClick={load}
				>
					{buttonText ||
						__('Load Comments', 'lazy-load-for-comments')}
				</button>
			)}
		</div>
	)
}

export default CommentsLoader
