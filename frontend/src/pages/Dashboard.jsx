import React, { useEffect, useState } from 'react';
import Layout from '../components/Layout';
import api from '../api/axios';
import { Package, Boxes, TrendingUp, AlertTriangle, Clock } from 'lucide-react';
import { ResponsiveContainer, AreaChart, Area, XAxis, YAxis, Tooltip, CartesianGrid } from 'recharts';

const Dashboard = () => {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchDashboardData();
  }, []);

  const fetchDashboardData = async () => {
    try {
      const res = await api.get('/reportes/dashboard');
      setData(res.data);
    } catch (err) {
      console.error('Error al cargar datos del dashboard', err);
    } finally {
      setLoading(false);
    }
  };

  const salesChartData = [
    { dia: 'Lun', total: 1500000 },
    { dia: 'Mar', total: 2300000 },
    { dia: 'Mié', total: 1800000 },
    { dia: 'Jue', total: 3200000 },
    { dia: 'Vie', total: 4500000 },
    { dia: 'Sáb', total: 2900000 },
    { dia: 'Dom', total: 1200000 },
  ];

  if (loading) {
    return (
      <Layout title="Dashboard General">
        <div className="flex items-center justify-center h-64 text-slate-500 font-medium">
          Cargando métricas y reportes...
        </div>
      </Layout>
    );
  }

  const productosPorVencer = data?.productos_por_vencer || [];
  const mermasDelMes = data?.mermas_del_mes?.total_mermas || 0;
  const ventasDelMes = data?.ventas_del_mes?.total || 0;
  const productosBajoStock = data?.productos_bajo_stock || [];

  return (
    <Layout title="Dashboard General">
      <div className="space-y-8">
        {/* KPI Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div className="bg-white border border-emerald-100 rounded-2xl p-6 flex items-center gap-4 shadow-sm">
            <div className="bg-emerald-100 text-[#2E7D32] p-3.5 rounded-xl">
              <Package className="w-7 h-7" />
            </div>
            <div>
              <p className="text-xs font-bold text-slate-500 uppercase">Productos Registrados</p>
              <h3 className="text-2xl font-bold text-slate-800 mt-1">10</h3>
            </div>
          </div>

          <div className="bg-white border border-emerald-100 rounded-2xl p-6 flex items-center gap-4 shadow-sm">
            <div className="bg-[#2E7D32] text-white p-3.5 rounded-xl shadow-sm">
              <TrendingUp className="w-7 h-7" />
            </div>
            <div>
              <p className="text-xs font-bold text-slate-500 uppercase">Ventas del Mes</p>
              <h3 className="text-2xl font-bold text-[#2E7D32] mt-1">
                ₲ {Number(ventasDelMes).toLocaleString('es-PY')}
              </h3>
            </div>
          </div>

          <div className="bg-white border border-emerald-100 rounded-2xl p-6 flex items-center gap-4 shadow-sm">
            <div className="bg-amber-100 text-amber-700 p-3.5 rounded-xl">
              <Clock className="w-7 h-7" />
            </div>
            <div>
              <p className="text-xs font-bold text-slate-500 uppercase">Por Vencer (30d)</p>
              <h3 className="text-2xl font-bold text-slate-800 mt-1">{productosPorVencer.length}</h3>
            </div>
          </div>

          <div className="bg-white border border-emerald-100 rounded-2xl p-6 flex items-center gap-4 shadow-sm">
            <div className="bg-red-100 text-red-700 p-3.5 rounded-xl">
              <AlertTriangle className="w-7 h-7" />
            </div>
            <div>
              <p className="text-xs font-bold text-slate-500 uppercase">Mermas del Mes</p>
              <h3 className="text-2xl font-bold text-slate-800 mt-1">{mermasDelMes} u.</h3>
            </div>
          </div>
        </div>

        {/* Sales Chart Section */}
        <div className="bg-white border border-emerald-100 rounded-2xl p-6 shadow-sm">
          <div className="flex items-center justify-between mb-6">
            <div>
              <h3 className="text-lg font-bold text-[#2E7D32]">Tendencia de Ventas (Últimos 7 días)</h3>
              <p className="text-xs text-slate-500">Comportamiento comercial en Guaraníes (₲)</p>
            </div>
          </div>
          <div className="h-72 w-full">
            <ResponsiveContainer width="100%" height="100%">
              <AreaChart data={salesChartData}>
                <defs>
                  <linearGradient id="colorSales" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="5%" stopColor="#2E7D32" stopOpacity={0.3}/>
                    <stop offset="95%" stopColor="#2E7D32" stopOpacity={0}/>
                  </linearGradient>
                </defs>
                <CartesianGrid strokeDasharray="3 3" stroke="#e2e8f0" />
                <XAxis dataKey="dia" stroke="#64748b" />
                <YAxis stroke="#64748b" />
                <Tooltip 
                  contentStyle={{ backgroundColor: '#ffffff', borderColor: '#cbd5e1', borderRadius: '12px', boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)' }} 
                  formatter={(val) => [`₲ ${Number(val).toLocaleString('es-PY')}`, 'Venta']}
                />
                <Area type="monotone" dataKey="total" stroke="#2E7D32" strokeWidth={3} fillOpacity={1} fill="url(#colorSales)" />
              </AreaChart>
            </ResponsiveContainer>
          </div>
        </div>

        {/* Alert Tables Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          {/* Lotes Por Vencer */}
          <div className="bg-white border border-emerald-100 rounded-2xl p-6 shadow-sm">
            <div className="flex items-center justify-between mb-4">
              <h3 className="text-base font-bold text-slate-800 flex items-center gap-2">
                <Clock className="w-5 h-5 text-amber-600" />
                Lotes Próximos a Vencer
              </h3>
            </div>
            {productosPorVencer.length === 0 ? (
              <p className="text-slate-500 text-sm py-4 text-center">No hay lotes próximos a vencer.</p>
            ) : (
              <div className="overflow-x-auto">
                <table className="w-full text-left text-sm text-slate-700">
                  <thead className="text-xs uppercase bg-[#F1F8E9] text-[#2E7D32] font-bold border-b border-emerald-100">
                    <tr>
                      <th className="px-4 py-3">Lote</th>
                      <th className="px-4 py-3">Stock</th>
                      <th className="px-4 py-3">Vencimiento</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-emerald-50">
                    {productosPorVencer.map((lote) => (
                      <tr key={lote.id} className="hover:bg-[#F1F8E9]/40">
                        <td className="px-4 py-3 font-medium text-slate-800">{lote.codigo_lote}</td>
                        <td className="px-4 py-3 text-[#2E7D32] font-bold">{lote.cantidad_actual}</td>
                        <td className="px-4 py-3 text-amber-700 font-mono text-xs font-semibold">{lote.fecha_vencimiento}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </div>

          {/* Bajo Stock Mínimo */}
          <div className="bg-white border border-emerald-100 rounded-2xl p-6 shadow-sm">
            <div className="flex items-center justify-between mb-4">
              <h3 className="text-base font-bold text-slate-800 flex items-center gap-2">
                <AlertTriangle className="w-5 h-5 text-red-600" />
                Productos con Bajo Stock
              </h3>
            </div>
            {productosBajoStock.length === 0 ? (
              <p className="text-slate-500 text-sm py-4 text-center">Todos los productos tienen niveles óptimos de stock.</p>
            ) : (
              <div className="overflow-x-auto">
                <table className="w-full text-left text-sm text-slate-700">
                  <thead className="text-xs uppercase bg-[#F1F8E9] text-[#2E7D32] font-bold border-b border-emerald-100">
                    <tr>
                      <th className="px-4 py-3">Producto</th>
                      <th className="px-4 py-3">Stock Actual</th>
                      <th className="px-4 py-3">Stock Mínimo</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-emerald-50">
                    {productosBajoStock.map((prod) => (
                      <tr key={prod.id} className="hover:bg-[#F1F8E9]/40">
                        <td className="px-4 py-3 font-medium text-slate-800">{prod.nombre}</td>
                        <td className="px-4 py-3 text-red-600 font-bold">{prod.stock_total || 0} {prod.unidad_medida}</td>
                        <td className="px-4 py-3 text-slate-500">{prod.stock_minimo} {prod.unidad_medida}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </div>
        </div>
      </div>
    </Layout>
  );
};

export default Dashboard;
