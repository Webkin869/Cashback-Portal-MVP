import { useEffect, useState } from 'react'
import api from '../services/api'

export default function ClicksPage() {
  const [rows, setRows] = useState([])
  useEffect(() => { api.get('/dashboard/clicks').then(({ data }) => setRows(data.clicks)) }, [])

  return (
    <div className="bg-white rounded-2xl border shadow-sm p-5">
      <h1 className="text-2xl font-bold mb-4">Klick-Aktivität</h1>
      <div className="space-y-3">
        {rows.map(row => (
          <div key={row.id} className="border rounded-xl p-4">
            <div className="font-semibold">{row.action_title}</div>
            <div className="text-sm text-slate-500 mt-1">Token: {row.click_token}</div>
            <div className="text-sm text-slate-500">Datum: {row.created_at}</div>
          </div>
        ))}
      </div>
    </div>
  )
}
