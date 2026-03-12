import { Link, Outlet } from 'react-router-dom'

export default function DashboardLayout() {
  const items = [
    ['/', 'Home'],
    ['/dashboard', 'Übersicht'],
    ['/dashboard/transactions', 'Transaktionen'],
    ['/dashboard/clicks', 'Klicks'],
    ['/dashboard/payouts', 'Auszahlungen'],
    ['/dashboard/referrals', 'Referrals'],
    ['/dashboard/tickets', 'Tickets']
  ]

  return (
    <div className="max-w-6xl mx-auto px-4 py-8 grid md:grid-cols-[240px_1fr] gap-6">
      <aside className="bg-white rounded-2xl shadow-sm border p-4 h-fit">
        <div className="font-bold mb-4">Dashboard</div>
        <div className="space-y-2">
          {items.map(([href, label]) => (
            <Link key={href} to={href} className="block px-3 py-2 rounded-xl hover:bg-slate-100">{label}</Link>
          ))}
        </div>
      </aside>
      <main>
        <Outlet />
      </main>
    </div>
  )
}
