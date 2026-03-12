import { Link } from 'react-router-dom'

export default function ActionCard({ action }) {
  return (
    <div className="bg-white rounded-2xl shadow-sm overflow-hidden border hover:shadow-md transition">
      <img src={action.banner_image} alt={action.title} className="w-full h-48 object-cover" />
      <div className="p-5">
        <div className="text-xs text-emerald-700 font-semibold mb-2 uppercase">{action.partner_network}</div>
        <h3 className="text-xl font-bold text-slate-900">{action.title}</h3>
        <p className="text-slate-600 mt-2 min-h-[48px]">{action.short_description}</p>
        <div className="mt-4 flex items-center justify-between">
          <div className="font-semibold text-emerald-700">
            {action.cashback_type === 'fixed' ? `${action.cashback_value} € Cashback` : `${action.cashback_value}% Cashback`}
          </div>
          <Link to={`/aktionen/${action.slug}`} className="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">Details</Link>
        </div>
      </div>
    </div>
  )
}
