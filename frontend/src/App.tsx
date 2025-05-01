import { AuthProvider, RequireAuth } from './context/authContext'
import { Route, Routes } from 'react-router-dom'
import { LoginScreen } from './screen/auth/LoginScreen'
import { LoadingScreen } from './screen/system/LoadingScreen'
import { useLoading } from './context/loadingContext'

function App() {
	const loading = useLoading();

	return (
		<AuthProvider>
			{loading.isLoading && <LoadingScreen />}
			<Routes>
				{/* Route per il login */}
				<Route path="/auth" element={<LoginScreen />} />
				{/* Route protette con RequireAuth */}
				<Route
					path="*"
					element={
						<RequireAuth>
							<></>
						</RequireAuth>
					}
				/>
			</Routes>
		</AuthProvider >
	)
}

export default App
