/**
 * Front end lazy load script.
 *
 * Vanilla JS on purpose — no React, no @wordpress/* imports. Keeping
 * the front end payload tiny is the whole point of this plugin.
 *
 * Reads config from `window.llcFrontend` (set via wp_localize_script)
 * and progressively enhances the placeholder rendered by PHP.
 */
import './styles/frontend.scss'

// URL hashes that mean "the visitor wants to see the comments".
const HASH_TRIGGERS = ['#comment', '#respond', '#llc-comments']

/**
 * Run a callback once the DOM is ready.
 *
 * @param {Function} fn Callback.
 */
const onReady = (fn) => {
	if (document.readyState !== 'loading') {
		fn()
	} else {
		document.addEventListener('DOMContentLoaded', fn, { once: true })
	}
}

/**
 * Build a DOM element with optional class names, text and attributes.
 *
 * @param {string} tag              Tag name.
 * @param {Object} [opts]           Options.
 * @param {string} [opts.className] Class list (space separated).
 * @param {string} [opts.text]      Text content.
 * @param {Object} [opts.attrs]     Attributes to set.
 * @return {HTMLElement} Element.
 */
const el = (tag, opts = {}) => {
	const node = document.createElement(tag)

	if (opts.className) {
		node.className = opts.className
	}

	if (opts.text != null) {
		node.textContent = opts.text
	}

	if (opts.attrs) {
		Object.keys(opts.attrs).forEach((key) => {
			node.setAttribute(key, opts.attrs[key])
		})
	}

	return node
}

/**
 * Initialise the loader for a single mount point.
 *
 * @param {HTMLElement} mount  The placeholder element.
 * @param {Object}      config Settings (from `window.llcFrontend`).
 */
const createLoader = (mount, config) => {
	const {
		postId,
		restUrl,
		restNonce,
		method,
		buttonText,
		buttonStyle,
		buttonClass,
		showLoader,
		isBlockTheme,
		loadingText,
		errorText,
		retryText,
	} = config

	let requested = false

	const renderSpinner = () => {
		mount.innerHTML = ''

		if (!showLoader) {
			return
		}

		const wrap = el('div', {
			className: 'llc-spinner',
			attrs: { 'aria-live': 'polite' },
		})

		wrap.appendChild(
			el('span', {
				className: 'llc-spinner-circle',
				attrs: { 'aria-hidden': 'true' },
			}),
		)

		wrap.appendChild(
			el('span', {
				className: 'screen-reader-text',
				text: loadingText,
			}),
		)

		mount.appendChild(wrap)
	}

	const renderError = () => {
		mount.innerHTML = ''

		const p = el('p', { className: 'llc-error' })
		p.appendChild(document.createTextNode(errorText + ' '))

		const retry = el('button', {
			className: 'llc-retry',
			text: retryText,
			attrs: { type: 'button' },
		})

		retry.addEventListener('click', () => {
			requested = false
			load()
		})

		p.appendChild(retry)
		mount.appendChild(p)
	}

	const renderLoaded = (html) => {
		mount.innerHTML = html

		// Re-init WP's comment reply handler so threaded replies work.
		if (
			window.addComment &&
			typeof window.addComment.init === 'function'
		) {
			window.addComment.init()
		}

		// Jump to the requested comment, if any.
		const hash = window.location.hash

		if (hash && hash.startsWith('#comment')) {
			const target = document.getElementById(hash.slice(1))

			if (target) {
				target.scrollIntoView()
			}
		}
	}

	const renderButton = () => {
		mount.innerHTML = ''

		const classes = ['llc-button']

		if (buttonStyle === 'custom') {
			classes.push('llc-button--custom')
		}

		// Block themes: inherit the theme's button styling from theme.json.
		if (buttonStyle === 'theme' && isBlockTheme) {
			classes.push('wp-element-button')
		}

		if (buttonClass) {
			classes.push(buttonClass)
		}

		const btn = el('button', {
			className: classes.join(' '),
			text: buttonText,
			attrs: { type: 'button' },
		})

		btn.addEventListener('click', load)

		// Wrap so the button sits centered without forcing text-align
		// on the mount (which would bleed into the loaded comments).
		const wrap = el('div', { className: 'llc-button-wrap' })
		wrap.appendChild(btn)
		mount.appendChild(wrap)
	}

	const load = async () => {
		// Guard against duplicate requests (scroll + hash, etc.).
		if (requested) {
			return
		}

		requested = true
		renderSpinner()

		try {
			const headers = { Accept: 'application/json' }

			// Send the REST nonce so the request runs as the current
			// user — otherwise the form renders as logged-out.
			if (restNonce) {
				headers['X-WP-Nonce'] = restNonce
			}

			const response = await fetch(
				`${restUrl}?post_id=${encodeURIComponent(postId)}`,
				{ headers, credentials: 'same-origin' },
			)

			if (!response.ok) {
				throw new Error('Request failed')
			}

			const data = await response.json()
			renderLoaded(data.html || '')
		} catch (e) {
			requested = false
			renderError()
		}
	}

	// 1. URL hash points at the comments → load immediately.
	const hash = window.location.hash

	if (hash && HASH_TRIGGERS.some((h) => hash.startsWith(h))) {
		load()
		return
	}

	// 2. Click method → render the button and wait.
	if (method === 'click') {
		renderButton()
		return
	}

	// 3. Scroll method → fall back to immediate load with no IO support.
	if (!('IntersectionObserver' in window)) {
		load()
		return
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

	observer.observe(mount)
}

onReady(() => {
	const mount = document.getElementById('lazy-load-for-comments-frontend')

	if (mount && window.llcFrontend) {
		createLoader(mount, window.llcFrontend)
	}
})
