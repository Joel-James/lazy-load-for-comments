import LoadingBehaviour from './loading-behaviour'
import LoadButton from './load-button'
import Cache from './cache'

/**
 * Settings tab — loading behaviour + cache management.
 */
const Settings = () => (
	<>
		<LoadingBehaviour />
		<LoadButton />
		<Cache />
	</>
)

export default Settings
