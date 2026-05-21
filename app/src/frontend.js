/**
 * Front end lazy load app entry point.
 */
import './styles/frontend.scss'
import domReady from '@wordpress/dom-ready'
import { createRoot } from '@wordpress/element'
import CommentsLoader from './frontend/comments-loader'

domReady(() => {
	const el = document.getElementById('lazy-load-for-comments-frontend')

	if (el && window.llcFrontend) {
		createRoot(el).render(<CommentsLoader {...window.llcFrontend} />)
	}
})
