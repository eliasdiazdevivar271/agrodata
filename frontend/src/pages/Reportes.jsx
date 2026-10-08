import React, { useEffect, useState } from 'react';
import Layout from '../components/Layout';
import api from '../api/axios';
import { BarChart3, AlertTriangle, Clock, TrendingUp } from 'lucide-react';

const Reportes = () => {
  const [porVencer, setPorVencer] = useState([]);
  const [mermas, setMermas] = useState(null);
  const [bajoStock, setBajoStock] = useState([]);
  const [ventasMes, setVentasMes] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchReportes();
  }, []);

  const fetchReportes = async () => {
    try {
      const [resV, resM, resB, resS] = await Promise.all([
        api.get('/reportes/por-vencer?dias=30'),
        api.get('/reportes/mermas'),
        api.get('/reportes/bajo-stock'),
        api.get('/reportes/ventas-mes')
      ]);
      setPorVencer(resV.data.data || []);
      setMermas(resM.data);
      setBajoStock(resB.data.data || []);
      setVentasMes(resS.data);
    } catch (err) {
      console.error('Error al cargar reportes', err);
    } finally {
      setLoading(false);
    }
  };

  return (
    <Layout title="Reportes Estratégicos y Financieros">
      <div className="space-y-8">
        {/* Total Summary Banner */}
        <div className="bg-gradient-to-r from-emerald-950 via-slate-950 to-slate-900 border border-emerald-800/40 rounded-2xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
          <div>
            <h3 className="text-xl font-bold text-slate-100 flex items-center gap-2">
              <BarChart3 className="w-6 h-6 text-emerald-400" />
              Consolidado Comercial Mensual
            </h3>
            <p className="text-sm text-slate-400 mt-1">Facturación total y control operativo de pérdidas</p>
          </div>
          <div className="flex items-center gap-6">
            <div className="text-right">
              <p className="text-xs text-slate-400 uppercase font-semibold">Total Ventas Mes</p>
              <p className="text-2xl font-extrabold text-emerald-400">
                ₲ {Number(ventasMes?.total || 0).toLocaleString('es-PY')}
              </p>
            </div>
            <div className="text-right border-l border-slate-800 pl-6">
              <p className="text-xs text-slate-400 uppercase font-semibold">Total Mermas</p>
              <p className="text-2xl font-extrabold text-red-400">
                {mermas?.total_mermas || 0} u.
              </p>
            </div>
          </div>
        </div>

        {/* Detailed Tables Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          {/* Por Vencer */}
          <div className="bg-slate-950 border border-slate-800 rounded-2xl p-6">
            <h4 className="text-lg font-bold text-slate-100 mb-4 flex items-center gap-2">
              <Clock className="w-5 h-5 text-amber-400" />
              Lotes Próximos a Vencer (30 Días)
            </h4>
            {loading ? (
              <p className="text-slate-400 text-sm">Cargando...</p>
            ) : porVencer.length === 0 ? (
              <p className="text-slate-500 text-sm py-4">No hay lotes próximos a vencer.</p>
            ) : (
              <table className="w-full text-left text-sm text-slate-300">
                <thead className="bg-slate-900 text-slate-400 uppercase text-xs">
                  <tr>
                    <th className="px-4 py-3">Lote</th>
                    <th className="px-4 py-3">Cant. Actual</th>
                    <th className="px-4 py-3">Vencimiento</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-800/60">
                  {porVencer.map((l) => (
                    <tr key={l.id}>
                      <td className="px-4 py-3 font-mono font-medium text-slate-200">{l.codigo_lote}</td>
                      <td className="px-4 py-3 text-emerald-400 font-bold">{l.cantidad_actual}</td>
                      <td className="px-4 py-3 text-amber-400 font-mono text-xs">{l.fecha_vencimiento}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            )}
          </div>

          {/* Bajo Stock */}
          <div className="bg-slate-950 border border-slate-800 rounded-2xl p-6">
            <h4 className="text-lg font-bold text-slate-100 mb-4 flex items-center gap-2">
              <AlertTriangle className="w-5 h-5 text-red-400" />
              Alerta de Stock Crítico (Menor al Mínimo)
            </h4>
            {loading ? (
              <p className="text-slate-400 text-sm">Cargando...</p>
            ) : bajoStock.length === 0 ? (
              <p className="text-slate-500 text-sm py-4">Sin alertas de stock bajo.</p>
            ) : (
              <table className="w-full text-left text-sm text-slate-300">
                <thead className="bg-slate-900 text-slate-400 uppercase text-xs">
                  <tr>
                    <th className="px-4 py-3">Producto</th>
                    <th className="px-4 py-3">Stock Actual</th>
                    <th className="px-4 py-3">Mínimo</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-800/60">
                  {bajoStock.map((p) => (
                    <tr key={p.id}>
                      <td className="px-4 py-3 font-medium text-slate-200">{p.nombre}</td>
                      <td className="px-4 py-3 text-red-400 font-bold">{p.stock_total || 0} {p.unidad_medida}</td>
                      <td className="px-4 py-3 text-slate-400">{p.stock_minimo} {p.unidad_medida}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            )}
          </div>
        </div>
      </div>
    </Layout>
  );
};

export default Reportes;
