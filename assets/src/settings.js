/**
 * Admin settings app entry point.
 */
import './admin/styles/settings.scss'
import domReady from '@wordpress/dom-ready'
import { createRoot } from '@wordpress/element'
import App from './admin'

domReady(() => {
	const el = document.getElementById('lazy-load-for-comments-settings')

	if (el) {
		createRoot(el).render(<App />)
	}
})
