import { useEffect, useState } from 'react'
import api from '../services/api'

export default function PayoutsPage() {
  const [rows, setRows] = useState([])
  const [amount, setAmount] = useState(25)
  const [provider, setProvider] = useState('Amazon')
  const [message, setMessage] = useState('')

  const load = () => api.get('/dashboard/payouts').then(({ data }) => setRows(data.payouts))
  useEffect(() => { load() }, [])

  const submit = async (e) => {
    e.preventDefault()
    try {
      const { data } = await api.post('/dashboard/payouts/request', { amount: Number(amount), provider })
      setMessage(data.message)
      load()
    } catch (err) {
      setMessage(err.response?.data?.message || 'Error')
    }
  }

  return (
    <div className="space-y-6">
      <form onSubmit={submit} className="bg-white rounded-2xl border shadow-sm p-5 flex flex-col md:flex-row gap-3 md:items-end">
        <div>
          <label className="block text-sm mb-1">Amount (€)</label>
          <input type="number" className="border rounded-xl px-4 py-3" value={amount} onChange={e => setAmount(e.target.value)} />
        </div>
        <div>
          <label className="block text-sm mb-1">Provider</label>
          <input className="border rounded-xl px-4 py-3" value={provider} onChange={e => setProvider(e.target.value)} />
        </div>
        <button className="bg-slate-900 text-white rounded-xl px-5 py-3">Request payout</button>
      </form>
      {message && <div className="text-sm text-emerald-700">{message}</div>}
      <div className="bg-white rounded-2xl border shadow-sm p-5">
        <h1 className="text-2xl font-bold mb-4">Auszahlungen</h1>
        <div className="space-y-3">
          {rows.map(row => <div key={row.id} className="border rounded-xl p-4">{row.provider} — {row.amount} € — {row.status}</div>)}
        </div>
      </div>
    </div>
  )
}
