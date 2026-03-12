import { useEffect, useState } from 'react'
import api from '../services/api'

export default function AdminPage() {
  const [data, setData] = useState({ actions: [], users: [], transactions: [], payouts: [], tickets: [] })
  const [error, setError] = useState('')

  useEffect(() => {
    Promise.all([
      api.get('/admin/actions'),
      api.get('/admin/users'),
      api.get('/admin/transactions'),
      api.get('/admin/payouts'),
      api.get('/admin/tickets')
    ]).then(([actions, users, transactions, payouts, tickets]) => {
      setData({
        actions: actions.data.actions,
        users: users.data.users,
        transactions: transactions.data.transactions,
        payouts: payouts.data.payouts,
        tickets: tickets.data.tickets,
      })
    }).catch(err => setError(err.response?.data?.message || 'Admin access error'))
  }, [])

  if (error) return <div className="max-w-6xl mx-auto px-4 py-8 text-red-600">{error}</div>

  return (
    <div className="max-w-6xl mx-auto px-4 py-8 space-y-6">
      <h1 className="text-3xl font-bold">Admin Übersicht</h1>
      <div className="grid md:grid-cols-5 gap-4">
        <div className="bg-white border rounded-2xl p-5">Aktionen: {data.actions.length}</div>
        <div className="bg-white border rounded-2xl p-5">Users: {data.users.length}</div>
        <div className="bg-white border rounded-2xl p-5">Transactions: {data.transactions.length}</div>
        <div className="bg-white border rounded-2xl p-5">Payouts: {data.payouts.length}</div>
        <div className="bg-white border rounded-2xl p-5">Tickets: {data.tickets.length}</div>
      </div>
      <div className="bg-white border rounded-2xl p-5">
        <h2 className="text-xl font-bold mb-3">Latest Transactions</h2>
        <div className="space-y-2">
          {data.transactions.slice(0, 10).map(t => <div key={t.id} className="border rounded-xl p-3">{t.email} — {t.action_title} — {t.status} — {t.cashback_value} €</div>)}
        </div>
      </div>
    </div>
  )
}
