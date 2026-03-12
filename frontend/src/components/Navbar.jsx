import { Link } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'

export default function Navbar() {
  const { user, logout } = useAuth()

  return (
    <header className="border-b bg-white sticky top-0 z-40">
      <div className="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
        <Link to="/" className="font-bold text-2xl text-slate-900">CashbackHub</Link>
        <nav className="flex items-center gap-4 text-sm">
          <Link to="/">Aktionen</Link>
          {user ? (
            <>
              <Link to="/dashboard">Dashboard</Link>
              {user.role === 'admin' && <Link to="/admin">Admin</Link>}
              <button onClick={logout} className="px-3 py-2 rounded-xl bg-slate-900 text-white">Logout</button>
            </>
          ) : (
            <>
              <Link to="/login">Login</Link>
              <Link to="/register" className="px-3 py-2 rounded-xl bg-slate-900 text-white">Registrieren</Link>
            </>
          )}
        </nav>
      </div>
    </header>
  )
}
