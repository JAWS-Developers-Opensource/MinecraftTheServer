import { createRoot } from 'react-dom/client'
import './index.css'
import App from './App.tsx'
import { BrowserRouter } from 'react-router-dom'
import { LoadingProvider } from './context/loadingContext.tsx'

createRoot(document.getElementById('root')!).render(
  <BrowserRouter>
    <LoadingProvider>
      <App />
    </LoadingProvider>
  </BrowserRouter>,
)
