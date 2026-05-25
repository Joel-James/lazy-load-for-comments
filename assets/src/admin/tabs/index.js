import { __ } from '@wordpress/i18n'
import Settings from './settings'
import Support from './support'

/**
 * Tab registry: key => { label, component }.
 *
 * New tabs (including future addons) only need to be added here.
 */
const tabs = {
	settings: {
		label: __('Settings', 'lazy-load-for-comments'),
		component: Settings,
	},
	support: {
		label: __('Support', 'lazy-load-for-comments'),
		component: Support,
	},
}

export default tabs
