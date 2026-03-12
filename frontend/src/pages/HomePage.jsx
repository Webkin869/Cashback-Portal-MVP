import { useEffect, useState } from 'react'
import api from '../services/api'
import ActionCard from '../components/ActionCard'

export default function HomePage() {
  const [actions, setActions] = useState([])

  useEffect(() => {
    api.get('/actions').then(({ data }) => setActions(data.actions))
  }, [])

  const featured = actions.filter(a => Number(a.is_featured) === 1).slice(0, 3)

  return (
    <div className="max-w-6xl mx-auto px-4 py-8 space-y-10">
      <section className="bg-gradient-to-r from-slate-900 to-slate-700 rounded-3xl p-8 text-white">
        <div className="text-sm uppercase tracking-[0.2em] text-slate-300">Cashback Portal</div>
        <h1 className="text-4xl font-bold mt-3">Top Aktionen mit echtem Cashback</h1>
        <p className="mt-3 text-slate-200 max-w-2xl">Entdecke Deals, sichere dir Cashback und verwalte alles bequem im Dashboard.</p>
      </section>

      {featured.length > 0 && (
        <section>
          <h2 className="text-2xl font-bold mb-4">Top Aktionen</h2>
          <div className="grid md:grid-cols-3 gap-6">
            {featured.map(action => <ActionCard key={action.id} action={action} />)}
          </div>
        </section>
      )}

      <section>
        <h2 className="text-2xl font-bold mb-4">Alle Aktionen</h2>
        <div className="grid md:grid-cols-3 gap-6">
          {actions.map(action => <ActionCard key={action.id} action={action} />)}
        </div>
      </section>
    </div>
  )
}
