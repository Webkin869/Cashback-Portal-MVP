import { useEffect, useState } from 'react'
import api from '../services/api'

export default function TicketsPage() {
  const [rows, setRows] = useState([])
  const [subject, setSubject] = useState('Nachbuchungsanfrage')
  const [message, setMessage] = useState('')
  const [notice, setNotice] = useState('')

  const load = () => api.get('/dashboard/tickets').then(({ data }) => setRows(data.tickets))
  useEffect(() => { load() }, [])

  const submit = async (e) => {
    e.preventDefault()
    try {
      const { data } = await api.post('/dashboard/tickets', { subject, message })
      setNotice(data.message)
      setMessage('')
      load()
    } catch (err) {
      setNotice(err.response?.data?.message || 'Error')
    }
  }

  return (
    <div className="space-y-6">
      <form onSubmit={submit} className="bg-white rounded-2xl border shadow-sm p-5 space-y-3">
        <h1 className="text-2xl font-bold">Neues Ticket</h1>
        <input className="w-full border rounded-xl px-4 py-3" value={subject} onChange={e => setSubject(e.target.value)} />
        <textarea className="w-full border rounded-xl px-4 py-3 min-h-32" value={message} onChange={e => setMessage(e.target.value)} placeholder="Beschreibe dein Problem..." />
        <button className="bg-slate-900 text-white rounded-xl px-5 py-3">Senden</button>
        {notice && <div className="text-sm text-emerald-700">{notice}</div>}
      </form>

      <div className="bg-white rounded-2xl border shadow-sm p-5">
        <h2 className="text-2xl font-bold mb-4">Meine Tickets</h2>
        <div className="space-y-3">
          {rows.map(row => <div key={row.id} className="border rounded-xl p-4">{row.subject} — {row.status}</div>)}
        </div>
      </div>
    </div>
  )
}
