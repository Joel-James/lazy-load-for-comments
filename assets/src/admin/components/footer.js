import { __ } from '@wordpress/i18n'
import { Button } from '@wordpress/components'
import useSettings from '../hooks/use-settings'

/**
 * Sticky footer with the save button.
 */
const Footer = () => {
	const { isSaving, isDirty, saveSettings } = useSettings()

	return (
		<div className="llc-footer">
			<Button
				variant="primary"
				icon={isSaving ? null : 'yes'}
				isBusy={isSaving}
				disabled={isSaving || !isDirty}
				onClick={saveSettings}
			>
				{isSaving
					? __('Saving…', 'lazy-load-for-comments')
					: __('Save Changes', 'lazy-load-for-comments')}
			</Button>
		</div>
	)
}

export default Footer
