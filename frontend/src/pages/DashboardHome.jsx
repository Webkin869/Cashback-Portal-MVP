import { useEffect, useState } from 'react'
import api from '../services/api'

export default function DashboardHome() {
  const [summary, setSummary] = useState(null)

  useEffect(() => {
    api.get('/dashboard/summary').then(({ data }) => setSummary(data.summary))
  }, [])

  if (!summary) return <div>Loading...</div>

  const cards = [
    ['Gesamt Cashback', `${summary.cashback} €`],
    ['Bestätigt', `${summary.confirmed} €`],
    ['Ausgezahlt', `${summary.paid} €`],
    ['Klicks', summary.clicks]
  ]

  return (
    <div className="space-y-6">
      <h1 className="text-3xl font-bold">Übersicht</h1>
      <div className="grid md:grid-cols-4 gap-4">
        {cards.map(([label, value]) => (
          <div key={label} className="bg-white rounded-2xl border shadow-sm p-5">
            <div className="text-sm text-slate-500">{label}</div>
            <div className="text-2xl font-bold mt-2">{value}</div>
          </div>
        ))}
      </div>
    </div>
  )
}
