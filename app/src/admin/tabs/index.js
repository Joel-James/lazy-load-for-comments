import { __ } from '@wordpress/i18n'
import General from './general'
import ButtonTab from './button'

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
	button: {
		label: __('Load Button', 'lazy-load-for-comments'),
		component: ButtonTab,
	},
}

export default tabs
