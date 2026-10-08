import React, { useEffect, useState } from 'react';
import Layout from '../components/Layout';
import api from '../api/axios';
import { ShoppingBag, Plus, X, Trash2 } from 'lucide-react';

const Compras = () => {
  const [compras, setCompras] = useState([]);
  const [proveedores, setProveedores] = useState([]);
  const [productos, setProductos] = useState([]);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);

  const [proveedorId, setProveedorId] = useState('');
  const [detalles, setDetalles] = useState([
    { producto_id: '', codigo_lote: '', cantidad: 1, precio_unitario: 0, fecha_vencimiento: '' }
  ]);

  useEffect(() => {
    fetchCompras();
    fetchAux();
  }, []);

  const fetchCompras = async () => {
    try {
      const res = await api.get('/compras');
      setCompras(res.data.data);
    } catch (err) {
      console.error('Error al cargar compras', err);
    } finally {
      setLoading(false);
    }
  };

  const fetchAux = async () => {
    try {
      const [resP, resProd] = await Promise.all([
        api.get('/proveedores'),
        api.get('/productos')
      ]);
      setProveedores(resP.data.data || []);
      setProductos(resProd.data.data || []);
    } catch (err) {
      console.error('Error al cargar auxiliares', err);
    }
  };

  const addDetalleRow = () => {
    setDetalles([
      ...detalles,
      { producto_id: '', codigo_lote: '', cantidad: 1, precio_unitario: 0, fecha_vencimiento: '' }
    ]);
  };

  const removeDetalleRow = (index) => {
    setDetalles(detalles.filter((_, i) => i !== index));
  };

  const handleDetalleChange = (index, field, value) => {
    const updated = [...detalles];
    updated[index][field] = value;
    setDetalles(updated);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const payload = {
        proveedor_id: proveedorId ? parseInt(proveedorId) : null,
        fecha: new Date().toISOString().split('T')[0],
        detalles: detalles.map(d => ({
          producto_id: parseInt(d.producto_id),
          codigo_lote: d.codigo_lote || `LOT-${Math.floor(1000 + Math.random() * 9000)}`,
          cantidad: parseInt(d.cantidad),
          precio_unitario: parseFloat(d.precio_unitario),
          fecha_vencimiento: d.fecha_vencimiento || new Date(Date.now() + 180*24*60*60*1000).toISOString().split('T')[0]
        }))
      };

      await api.post('/compras', payload);
      setIsModalOpen(false);
      setDetalles([{ producto_id: '', codigo_lote: '', cantidad: 1, precio_unitario: 0, fecha_vencimiento: '' }]);
      fetchCompras();
    } catch (err) {
      alert(err.response?.data?.message || 'Error al registrar la compra');
    }
  };

  return (
    <Layout title="Gestión de Compras y Generación de Lotes">
      <div className="space-y-6">
        <div className="flex justify-end">
          <button
            onClick={() => setIsModalOpen(true)}
            className="flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-xl text-sm transition-all shadow-lg shadow-emerald-950/40"
          >
            <Plus className="w-4 h-4" />
            Nueva Compra / Ingreso
          </button>
        </div>

        {/* Compras Table */}
        <div className="bg-slate-950 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
          {loading ? (
            <div className="p-8 text-center text-slate-400">Cargando compras...</div>
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full text-left text-sm text-slate-300">
                <thead className="bg-slate-900 text-slate-400 uppercase text-xs border-b border-slate-800">
                  <tr>
                    <th className="px-6 py-4">ID</th>
                    <th className="px-6 py-4">Proveedor</th>
                    <th className="px-6 py-4">Fecha</th>
                    <th className="px-6 py-4">Items / Lotes Generados</th>
                    <th className="px-6 py-4">Total (₲)</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-800/60">
                  {compras.map((c) => (
                    <tr key={c.id} className="hover:bg-slate-900/40 transition-colors">
                      <td className="px-6 py-4 text-slate-500 font-mono">#{c.id}</td>
                      <td className="px-6 py-4 font-semibold text-slate-100">
                        {c.proveedor?.nombre || 'Proveedor Ocasional'}
                      </td>
                      <td className="px-6 py-4 text-slate-400 font-mono text-xs">{c.fecha}</td>
                      <td className="px-6 py-4">
                        <div className="space-y-1">
                          {c.detalles?.map((d, i) => (
                            <div key={i} className="text-xs text-slate-300">
                              • {d.producto?.nombre} [{d.lote?.codigo_lote}] x {d.cantidad} (₲ {Number(d.precio_unitario).toLocaleString('es-PY')})
                            </div>
                          ))}
                        </div>
                      </td>
                      <td className="px-6 py-4 font-bold text-emerald-400 text-base">
                        ₲ {Number(c.total).toLocaleString('es-PY')}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>
      </div>

      {/* New Compra Modal */}
      {isModalOpen && (
        <div className="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
          <div className="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-2xl p-6 shadow-2xl">
            <div className="flex items-center justify-between pb-4 border-b border-slate-800">
              <h3 className="text-lg font-bold text-slate-100 flex items-center gap-2">
                <ShoppingBag className="w-5 h-5 text-emerald-400" />
                Registrar Nueva Compra
              </h3>
              <button onClick={() => setIsModalOpen(false)} className="text-slate-400 hover:text-slate-200">
                <X className="w-5 h-5" />
              </button>
            </div>

            <form onSubmit={handleSubmit} className="mt-6 space-y-6">
              <div>
                <label className="block text-xs font-semibold text-slate-300 uppercase mb-1">Proveedor</label>
                <select
                  value={proveedorId}
                  onChange={(e) => setProveedorId(e.target.value)}
                  className="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-100 text-sm focus:outline-none focus:border-emerald-500"
                >
                  <option value="">-- Seleccionar Proveedor --</option>
                  {proveedores.map(p => (
                    <option key={p.id} value={p.id}>{p.nombre} (RUC: {p.ruc || 'N/A'})</option>
                  ))}
                </select>
              </div>

              <div>
                <div className="flex items-center justify-between mb-2">
                  <label className="text-xs font-semibold text-slate-300 uppercase">Lotes a Ingresar</label>
                  <button
                    type="button"
                    onClick={addDetalleRow}
                    className="text-xs text-emerald-400 hover:underline font-medium"
                  >
                    + Agregar Lote
                  </button>
                </div>

                <div className="space-y-3">
                  {detalles.map((det, idx) => (
                    <div key={idx} className="flex flex-col gap-2 bg-slate-950 p-3 rounded-xl border border-slate-800">
                      <div className="flex items-center gap-3">
                        <select
                          required
                          value={det.producto_id}
                          onChange={(e) => handleDetalleChange(idx, 'producto_id', e.target.value)}
                          className="flex-1 px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 text-xs focus:outline-none focus:border-emerald-500"
                        >
                          <option value="">-- Seleccionar Producto --</option>
                          {productos.map(p => (
                            <option key={p.id} value={p.id}>{p.nombre}</option>
                          ))}
                        </select>

                        <input
                          type="text"
                          placeholder="Cód. Lote (e.g. LOT-101)"
                          value={det.codigo_lote}
                          onChange={(e) => handleDetalleChange(idx, 'codigo_lote', e.target.value)}
                          className="w-36 px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 text-xs"
                        />
                      </div>

                      <div className="flex items-center gap-3">
                        <input
                          type="number"
                          min="1"
                          placeholder="Cantidad"
                          required
                          value={det.cantidad}
                          onChange={(e) => handleDetalleChange(idx, 'cantidad', e.target.value)}
                          className="w-28 px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 text-xs"
                        />

                        <input
                          type="number"
                          min="0"
                          placeholder="Precio Costo U."
                          required
                          value={det.precio_unitario}
                          onChange={(e) => handleDetalleChange(idx, 'precio_unitario', e.target.value)}
                          className="w-32 px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 text-xs"
                        />

                        <input
                          type="date"
                          required
                          value={det.fecha_vencimiento}
                          onChange={(e) => handleDetalleChange(idx, 'fecha_vencimiento', e.target.value)}
                          className="flex-1 px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 text-xs"
                        />

                        {detalles.length > 1 && (
                          <button
                            type="button"
                            onClick={() => removeDetalleRow(idx)}
                            className="p-1.5 text-red-400 hover:bg-red-500/10 rounded-lg"
                          >
                            <Trash2 className="w-4 h-4" />
                          </button>
                        )}
                      </div>
                    </div>
                  ))}
                </div>
              </div>

              <div className="pt-4 border-t border-slate-800 flex justify-end gap-3">
                <button
                  type="button"
                  onClick={() => setIsModalOpen(false)}
                  className="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-medium"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-sm font-medium shadow-md shadow-emerald-950/30"
                >
                  Registrar Compra
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </Layout>
  );
};

export default Compras;
