import LoadingBehaviour from './loading-behaviour'
import LoadButton from './load-button'
import Cache from './cache'

/**
 * General settings tab — loading behaviour + cache management.
 */
const General = () => (
	<>
		<LoadingBehaviour />
		<LoadButton />
		<Cache />
	</>
)

export default General
