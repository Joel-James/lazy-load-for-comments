import { __ } from '@wordpress/i18n'
import { useState } from '@wordpress/element'
import { Placeholder, Spinner } from '@wordpress/components'
import useSettings from './hooks/use-settings'
import tabs from './tabs'
import PageHeader from './components/page-header'
import PageBody from './components/page-body'
import TabNav from './components/tab-nav'
import Footer from './components/footer'
import Notices from './components/notices'

/**
 * Root settings page component.
 */
const App = () => {
	const { hasLoaded } = useSettings()
	const [current, setCurrent] = useState('settings')

	const navs = Object.fromEntries(
		Object.entries(tabs).map(([key, tab]) => [key, tab.label]),
	)

	const ActiveTab = (tabs[current] || tabs.settings).component

	return (
		<>
			<PageHeader
				title={__('Lazy Load for Comments', 'lazy-load-for-comments')}
			>
				<TabNav current={current} navs={navs} onChange={setCurrent} />
			</PageHeader>

			<PageBody>
				{!hasLoaded ? (
					<Placeholder>
						<Spinner />
					</Placeholder>
				) : (
					<>
						<ActiveTab />
						<Footer />
					</>
				)}
			</PageBody>

			<Notices />
		</>
	)
}

export default App
