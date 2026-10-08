import React, { useEffect, useState } from 'react';
import Layout from '../components/Layout';
import api from '../api/axios';
import { Boxes, Calendar, Tag } from 'lucide-react';

const Lotes = () => {
  const [lotes, setLotes] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchLotes();
  }, []);

  const fetchLotes = async () => {
    try {
      const res = await api.get('/lotes');
      setLotes(res.data.data);
    } catch (err) {
      console.error('Error al cargar lotes', err);
    } finally {
      setLoading(false);
    }
  };

  return (
    <Layout title="Control de Lotes y Vencimientos">
      <div className="space-y-6">
        <div className="bg-slate-950 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
          {loading ? (
            <div className="p-8 text-center text-slate-400">Cargando lotes...</div>
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full text-left text-sm text-slate-300">
                <thead className="bg-slate-900 text-slate-400 uppercase text-xs border-b border-slate-800">
                  <tr>
                    <th className="px-6 py-4">Código Lote</th>
                    <th className="px-6 py-4">Producto</th>
                    <th className="px-6 py-4">Cant. Inicial</th>
                    <th className="px-6 py-4">Cant. Actual</th>
                    <th className="px-6 py-4">Ingreso</th>
                    <th className="px-6 py-4">Vencimiento</th>
                    <th className="px-6 py-4">Estado</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-800/60">
                  {lotes.map((lote) => {
                    const isVencido = new Date(lote.fecha_vencimiento) < new Date();
                    return (
                      <tr key={lote.id} className="hover:bg-slate-900/40 transition-colors">
                        <td className="px-6 py-4 font-mono font-semibold text-emerald-400">
                          {lote.codigo_lote}
                        </td>
                        <td className="px-6 py-4 font-medium text-slate-100">
                          {lote.producto?.nombre || 'Desconocido'}
                        </td>
                        <td className="px-6 py-4 text-slate-400">{lote.cantidad_inicial}</td>
                        <td className="px-6 py-4 font-bold text-slate-100">{lote.cantidad_actual}</td>
                        <td className="px-6 py-4 text-slate-400 text-xs font-mono">{lote.fecha_ingreso}</td>
                        <td className="px-6 py-4 font-mono text-xs text-amber-400">{lote.fecha_vencimiento}</td>
                        <td className="px-6 py-4">
                          {lote.cantidad_actual === 0 ? (
                            <span className="bg-slate-800 text-slate-400 text-xs px-2.5 py-1 rounded-full border border-slate-700">Agotado</span>
                          ) : isVencido ? (
                            <span className="bg-red-500/20 text-red-400 text-xs px-2.5 py-1 rounded-full border border-red-500/30">Vencido</span>
                          ) : (
                            <span className="bg-emerald-500/20 text-emerald-400 text-xs px-2.5 py-1 rounded-full border border-emerald-500/30">Activo</span>
                          )}
                        </td>
                      </tr>
                    );
                  })}
                </tbody>
              </table>
            </div>
          )}
        </div>
      </div>
    </Layout>
  );
};

export default Lotes;
