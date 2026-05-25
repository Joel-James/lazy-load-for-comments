/**
 * Settings hook.
 *
 * Reads and writes the plugin settings through the WordPress
 * `/wp/v2/settings` endpoint via the core-data store. The option is
 * registered server side with `show_in_rest`, so it behaves like any
 * other site setting.
 */
import { __ } from '@wordpress/i18n'
import { useDispatch, useSelect } from '@wordpress/data'
import { store as noticesStore } from '@wordpress/notices'
import { store as coreStore, useEntityProp } from '@wordpress/core-data'

const OPTION_KEY = 'lazy_load_for_comments_settings'

const useSettings = () => {
	const [option, setOption] = useEntityProp('root', 'site', OPTION_KEY)

	const { saveEditedEntityRecord } = useDispatch(coreStore)
	const { createSuccessNotice, createErrorNotice, removeAllNotices } =
		useDispatch(noticesStore)

	const { isSaving, hasLoaded, isDirty } = useSelect((select) => {
		const core = select(coreStore)

		return {
			isSaving: core.isSavingEntityRecord('root', 'site'),
			isDirty: core.hasEditsForEntityRecord('root', 'site'),
			hasLoaded: core.hasFinishedResolution('getEntityRecord', [
				'root',
				'site',
			]),
		}
	}, [])

	// Settings always resolve to an object once loaded.
	const settings = option || {}

	/**
	 * Get a single setting value.
	 *
	 * @param {string} key          Setting key.
	 * @param {*}       defaultValue Fallback value.
	 * @return {*} Setting value.
	 */
	const getSetting = (key, defaultValue = '') => settings[key] ?? defaultValue

	/**
	 * Update a single setting value (locally, until saved).
	 *
	 * @param {string} key   Setting key.
	 * @param {*}       value New value.
	 */
	const setSetting = (key, value) => {
		setOption({ ...settings, [key]: value })
	}

	/**
	 * Persist the edited settings via the REST API.
	 */
	const saveSettings = async () => {
		removeAllNotices()

		const saved = await saveEditedEntityRecord('root', 'site')

		if (saved) {
			createSuccessNotice(
				__('Settings saved.', 'lazy-load-for-comments'),
				{
					type: 'snackbar',
				},
			)
		} else {
			createErrorNotice(
				__('Could not save settings.', 'lazy-load-for-comments'),
				{ type: 'snackbar' },
			)
		}
	}

	return {
		settings,
		hasLoaded,
		isSaving,
		isDirty,
		getSetting,
		setSetting,
		saveSettings,
	}
}

export default useSettings
