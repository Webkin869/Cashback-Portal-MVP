import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'

export default function LoginPage() {
  const [email, setEmail] = useState('user@example.com')
  const [password, setPassword] = useState('password123')
  const [error, setError] = useState('')
  const { login } = useAuth()
  const navigate = useNavigate()

  const submit = async (e) => {
    e.preventDefault()
    setError('')
    try {
      await login(email, password)
      navigate('/dashboard')
    } catch (err) {
      setError(err.response?.data?.message || 'Login failed')
    }
  }

  return (
    <div className="max-w-md mx-auto px-4 py-10">
      <form onSubmit={submit} className="bg-white rounded-3xl border shadow-sm p-8 space-y-4">
        <h1 className="text-3xl font-bold">Login</h1>
        <input className="w-full border rounded-xl px-4 py-3" value={email} onChange={e => setEmail(e.target.value)} placeholder="Email" />
        <input type="password" className="w-full border rounded-xl px-4 py-3" value={password} onChange={e => setPassword(e.target.value)} placeholder="Password" />
        {error && <div className="text-red-600 text-sm">{error}</div>}
        <button className="w-full bg-slate-900 text-white rounded-xl py-3">Einloggen</button>
      </form>
    </div>
  )
}
