import React, { useEffect, useState } from 'react';
import Layout from '../components/Layout';
import api from '../api/axios';
import { ArrowLeftRight } from 'lucide-react';

const Movimientos = () => {
  const [movimientos, setMovimientos] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchMovimientos();
  }, []);

  const fetchMovimientos = async () => {
    try {
      const res = await api.get('/movimientos');
      setMovimientos(res.data.data);
    } catch (err) {
      console.error('Error al cargar movimientos', err);
    } finally {
      setLoading(false);
    }
  };

  return (
    <Layout title="Kardex e Historial de Movimientos">
      <div className="space-y-6">
        <div className="bg-slate-950 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
          {loading ? (
            <div className="p-8 text-center text-slate-400">Cargando movimientos...</div>
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full text-left text-sm text-slate-300">
                <thead className="bg-slate-900 text-slate-400 uppercase text-xs border-b border-slate-800">
                  <tr>
                    <th className="px-6 py-4">ID</th>
                    <th className="px-6 py-4">Lote</th>
                    <th className="px-6 py-4">Tipo</th>
                    <th className="px-6 py-4">Cantidad</th>
                    <th className="px-6 py-4">Motivo / Detalle</th>
                    <th className="px-6 py-4">Fecha</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-800/60">
                  {movimientos.map((m) => (
                    <tr key={m.id} className="hover:bg-slate-900/40 transition-colors">
                      <td className="px-6 py-4 text-slate-500 font-mono">#{m.id}</td>
                      <td className="px-6 py-4 font-mono font-medium text-slate-200">
                        {m.lote?.codigo_lote || 'N/A'}
                      </td>
                      <td className="px-6 py-4">
                        {m.tipo === 'entrada' || m.tipo === 'compra' ? (
                          <span className="bg-emerald-500/20 text-emerald-400 text-xs px-2.5 py-1 rounded-full border border-emerald-500/30 uppercase">Entrada</span>
                        ) : m.tipo === 'merma' ? (
                          <span className="bg-red-500/20 text-red-400 text-xs px-2.5 py-1 rounded-full border border-red-500/30 uppercase">Merma</span>
                        ) : (
                          <span className="bg-blue-500/20 text-blue-400 text-xs px-2.5 py-1 rounded-full border border-blue-500/30 uppercase">Salida</span>
                        )}
                      </td>
                      <td className="px-6 py-4 font-bold text-slate-100">{m.cantidad}</td>
                      <td className="px-6 py-4 text-slate-400">{m.motivo || 'N/A'}</td>
                      <td className="px-6 py-4 text-xs text-slate-500 font-mono">{m.created_at || m.fecha}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>
      </div>
    </Layout>
  );
};

export default Movimientos;
