import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'

export default function RegisterPage() {
  const [form, setForm] = useState({ name: '', email: '', password: '', referral_code: '' })
  const [error, setError] = useState('')
  const { register } = useAuth()
  const navigate = useNavigate()

  const submit = async (e) => {
    e.preventDefault()
    setError('')
    try {
      await register(form)
      navigate('/dashboard')
    } catch (err) {
      setError(err.response?.data?.message || 'Register failed')
    }
  }

  return (
    <div className="max-w-md mx-auto px-4 py-10">
      <form onSubmit={submit} className="bg-white rounded-3xl border shadow-sm p-8 space-y-4">
        <h1 className="text-3xl font-bold">Registrieren</h1>
        <input className="w-full border rounded-xl px-4 py-3" placeholder="Name" onChange={e => setForm({ ...form, name: e.target.value })} />
        <input className="w-full border rounded-xl px-4 py-3" placeholder="Email" onChange={e => setForm({ ...form, email: e.target.value })} />
        <input type="password" className="w-full border rounded-xl px-4 py-3" placeholder="Password" onChange={e => setForm({ ...form, password: e.target.value })} />
        <input className="w-full border rounded-xl px-4 py-3" placeholder="Referral code (optional)" onChange={e => setForm({ ...form, referral_code: e.target.value })} />
        {error && <div className="text-red-600 text-sm">{error}</div>}
        <button className="w-full bg-slate-900 text-white rounded-xl py-3">Konto erstellen</button>
      </form>
    </div>
  )
}
