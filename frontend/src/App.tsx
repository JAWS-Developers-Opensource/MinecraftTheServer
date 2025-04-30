import { useState } from 'react'
import reactLogo from './assets/react.svg'
import viteLogo from '/vite.svg'
import './App.css'
import { AuthProvider } from './context/authContext'

function App() {
  const [count, setCount] = useState(0)

  return (
    <AuthProvider>
      <Router>
        <Routes>
            {/* Route per il login */}
            <Route path="/auth" element={<Login />} />
            <Route path="/dev-info" element={<InfoPage />} />
            {/* Route protette con RequireAuth */}
            <Route
                path="*"
                element={
                    <RequireAuth>
                        <DashboardLayout />
                    </RequireAuth>
                }
            />
        </Routes>
    </Router>
    </AuthProvider>
  )
}

export default App
