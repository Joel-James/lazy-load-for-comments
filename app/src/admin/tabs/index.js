import { __ } from '@wordpress/i18n'
import General from './general'
import Support from './support'

/**
 * Tab registry: key => { label, component }.
 *
 * New tabs (including future addons) only need to be added here.
 */
const tabs = {
	general: {
		label: __('General', 'lazy-load-for-comments'),
		component: General,
	},
	support: {
		label: __('Support', 'lazy-load-for-comments'),
		component: Support,
	},
}

export default tabs
