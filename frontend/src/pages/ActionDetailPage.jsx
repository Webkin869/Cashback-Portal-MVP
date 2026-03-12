import { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import api from '../services/api'
import { useAuth } from '../context/AuthContext'

export default function ActionDetailPage() {
  const { slug } = useParams()
  const [action, setAction] = useState(null)
  const [message, setMessage] = useState('')
  const { user } = useAuth()
  const navigate = useNavigate()

  useEffect(() => {
    api.get(`/actions/${slug}`).then(({ data }) => setAction(data.action))
  }, [slug])

  const handleParticipate = async () => {
    if (!user) {
      alert('Iltimos, oldin login yoki registratsiya qiling.')
      navigate('/login')
      return
    }

    const { data } = await api.post(`/actions/${action.id}/click`)
    setMessage('Click yozildi. Redirect URL console ga chiqarildi.')
    window.open(data.redirect_url, '_blank')
  }

  if (!action) return <div className="max-w-4xl mx-auto px-4 py-8">Loading...</div>

  return (
    <div className="max-w-4xl mx-auto px-4 py-8">
      <div className="bg-white rounded-3xl border shadow-sm overflow-hidden">
        <img src={action.banner_image} alt={action.title} className="w-full h-80 object-cover" />
        <div className="p-8">
          <div className="text-sm font-semibold uppercase text-emerald-700">{action.partner_network}</div>
          <h1 className="text-4xl font-bold mt-2">{action.title}</h1>
          <p className="text-slate-600 mt-4">{action.description}</p>

          <div className="mt-6 grid md:grid-cols-2 gap-4">
            <div className="p-4 rounded-2xl bg-slate-50 border">
              <div className="text-sm text-slate-500">Cashback</div>
              <div className="font-bold text-xl mt-1">
                {action.cashback_type === 'fixed' ? `${action.cashback_value} €` : `${action.cashback_value}%`}
              </div>
            </div>
            <div className="p-4 rounded-2xl bg-slate-50 border">
              <div className="text-sm text-slate-500">Teilnahmebedingungen</div>
              <div className="font-medium mt-1">{action.terms}</div>
            </div>
          </div>

          <button onClick={handleParticipate} className="mt-8 px-6 py-3 rounded-2xl bg-emerald-600 text-white font-semibold">
            Prämie sichern
          </button>

          {message && <div className="mt-4 text-emerald-700">{message}</div>}
        </div>
      </div>
    </div>
  )
}
