import { useSelect, useDispatch } from '@wordpress/data'
import { store as noticesStore } from '@wordpress/notices'
import { SnackbarList } from '@wordpress/components'

/**
 * Renders the snackbar notices created while saving settings.
 */
const Notices = () => {
	const notices = useSelect(
		(select) =>
			select(noticesStore)
				.getNotices()
				.filter((notice) => notice.type === 'snackbar'),
		[],
	)

	const { removeNotice } = useDispatch(noticesStore)

	if (!notices.length) {
		return null
	}

	return (
		<SnackbarList
			className="llc-notices"
			notices={notices}
			onRemove={removeNotice}
		/>
	)
}

export default Notices
