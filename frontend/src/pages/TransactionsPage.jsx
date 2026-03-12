import { useEffect, useState } from 'react'
import api from '../services/api'

export default function TransactionsPage() {
  const [rows, setRows] = useState([])
  useEffect(() => { api.get('/dashboard/transactions').then(({ data }) => setRows(data.transactions)) }, [])

  return (
    <div className="bg-white rounded-2xl border shadow-sm p-5">
      <h1 className="text-2xl font-bold mb-4">Transaktionen</h1>
      <div className="overflow-auto">
        <table className="w-full text-sm">
          <thead><tr className="text-left border-b"><th className="py-2">Aktion</th><th>Status</th><th>Cashback</th><th>Order</th></tr></thead>
          <tbody>
            {rows.map(row => (
              <tr key={row.id} className="border-b">
                <td className="py-3">{row.action_title}</td>
                <td>{row.status}</td>
                <td>{row.cashback_value} €</td>
                <td>{row.order_value} €</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  )
}
