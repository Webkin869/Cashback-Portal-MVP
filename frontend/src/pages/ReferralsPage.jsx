import { useEffect, useState } from 'react'
import api from '../services/api'

export default function ReferralsPage() {
  const [data, setData] = useState({ referrals: [], my_code: '' })
  useEffect(() => { api.get('/dashboard/referrals').then(({ data }) => setData(data)) }, [])

  return (
    <div className="space-y-6">
      <div className="bg-white rounded-2xl border shadow-sm p-5">
        <div className="text-sm text-slate-500">Dein Referral Code</div>
        <div className="text-3xl font-bold mt-2">{data.my_code}</div>
      </div>
      <div className="bg-white rounded-2xl border shadow-sm p-5">
        <h1 className="text-2xl font-bold mb-4">Geworbene Freunde</h1>
        <div className="space-y-3">
          {data.referrals.map(row => <div key={row.id} className="border rounded-xl p-4">{row.referred_name} — Bonus: {row.signup_bonus} € — Share: {row.cashback_share_percent}%</div>)}
        </div>
      </div>
    </div>
  )
}
