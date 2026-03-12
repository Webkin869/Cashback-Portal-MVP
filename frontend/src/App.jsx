import { Navigate, Route, Routes } from 'react-router-dom'
import Navbar from './components/Navbar'
import HomePage from './pages/HomePage'
import ActionDetailPage from './pages/ActionDetailPage'
import LoginPage from './pages/LoginPage'
import RegisterPage from './pages/RegisterPage'
import DashboardLayout from './layouts/DashboardLayout'
import DashboardHome from './pages/DashboardHome'
import TransactionsPage from './pages/TransactionsPage'
import ClicksPage from './pages/ClicksPage'
import PayoutsPage from './pages/PayoutsPage'
import ReferralsPage from './pages/ReferralsPage'
import TicketsPage from './pages/TicketsPage'
import AdminPage from './pages/AdminPage'
import { useAuth } from './context/AuthContext'

function Protected({ children }) {
  const { user, loading } = useAuth()
  if (loading) return <div className="p-10">Loading...</div>
  return user ? children : <Navigate to="/login" />
}

export default function App() {
  return (
    <div>
      <Navbar />
      <Routes>
        <Route path="/" element={<HomePage />} />
        <Route path="/aktionen/:slug" element={<ActionDetailPage />} />
        <Route path="/login" element={<LoginPage />} />
        <Route path="/register" element={<RegisterPage />} />
        <Route path="/dashboard" element={<Protected><DashboardLayout /></Protected>}>
          <Route index element={<DashboardHome />} />
          <Route path="transactions" element={<TransactionsPage />} />
          <Route path="clicks" element={<ClicksPage />} />
          <Route path="payouts" element={<PayoutsPage />} />
          <Route path="referrals" element={<ReferralsPage />} />
          <Route path="tickets" element={<TicketsPage />} />
        </Route>
        <Route path="/admin" element={<Protected><AdminPage /></Protected>} />
      </Routes>
    </div>
  )
}
