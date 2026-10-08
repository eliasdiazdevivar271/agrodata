import React, { useEffect, useState } from 'react';
import Layout from '../components/Layout';
import api from '../api/axios';
import { ShoppingCart, Plus, X, Trash2 } from 'lucide-react';
import toast from 'react-hot-toast';

const Ventas = () => {
  const [ventas, setVentas] = useState([]);
  const [clientes, setClientes] = useState([]);
  const [productos, setProductos] = useState([]);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);

  const [clienteId, setClienteId] = useState('');
  const [detalles, setDetalles] = useState([
    { producto_id: '', cantidad: 1, precio_unitario: 0 }
  ]);

  useEffect(() => {
    fetchVentas();
    fetchClientesYProductos();
  }, []);

  const fetchVentas = async () => {
    try {
      const res = await api.get('/ventas');
      setVentas(res.data.data);
    } catch (err) {
      console.error('Error al cargar ventas', err);
      toast.error('Error al cargar ventas');
    } finally {
      setLoading(false);
    }
  };

  const fetchClientesYProductos = async () => {
    try {
      const [resC, resP] = await Promise.all([
        api.get('/clientes'),
        api.get('/productos')
      ]);
      setClientes(resC.data.data || []);
      setProductos(resP.data.data || []);
    } catch (err) {
      console.error('Error al cargar clientes/productos', err);
    }
  };

  const addDetalleRow = () => {
    setDetalles([...detalles, { producto_id: '', cantidad: 1, precio_unitario: 0 }]);
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
        cliente_id: clienteId ? parseInt(clienteId) : null,
        fecha: new Date().toISOString().split('T')[0],
        detalles: detalles.map(d => ({
          producto_id: parseInt(d.producto_id),
          cantidad: parseInt(d.cantidad),
          precio_unitario: parseFloat(d.precio_unitario)
        }))
      };

      await api.post('/ventas', payload);
      toast.success('Venta procesada exitosamente con lote FIFO');
      setIsModalOpen(false);
      setDetalles([{ producto_id: '', cantidad: 1, precio_unitario: 0 }]);
      fetchVentas();
    } catch (err) {
      toast.error(err.response?.data?.message || 'Error al procesar la venta');
    }
  };

  return (
    <Layout title="Registro y Control de Ventas">
      <div className="space-y-6">
        <div className="flex justify-end">
          <button
            onClick={() => setIsModalOpen(true)}
            className="flex items-center gap-2 px-4 py-2.5 bg-[#2E7D32] hover:bg-[#1b5e20] text-white font-medium rounded-xl text-sm transition-all shadow-md shadow-emerald-900/20"
          >
            <Plus className="w-4 h-4" />
            Nueva Venta (FIFO)
          </button>
        </div>

        {/* Ventas Table */}
        <div className="bg-white border border-emerald-100 rounded-2xl overflow-hidden shadow-sm">
          {loading ? (
            <div className="p-8 text-center text-slate-500 font-medium">Cargando ventas...</div>
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full text-left text-sm text-slate-700">
                <thead className="bg-[#F1F8E9] text-[#2E7D32] font-bold uppercase text-xs border-b border-emerald-100">
                  <tr>
                    <th className="px-6 py-4">ID</th>
                    <th className="px-6 py-4">Cliente</th>
                    <th className="px-6 py-4">Fecha</th>
                    <th className="px-6 py-4">Detalles / Items</th>
                    <th className="px-6 py-4">Total (₲)</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-emerald-50">
                  {ventas.map((v) => (
                    <tr key={v.id} className="hover:bg-[#F1F8E9]/40 transition-colors">
                      <td className="px-6 py-4 text-slate-400 font-mono">#{v.id}</td>
                      <td className="px-6 py-4 font-semibold text-slate-800">
                        {v.cliente?.nombre || 'Cliente Ocasional'}
                      </td>
                      <td className="px-6 py-4 text-slate-500 font-mono text-xs">{v.fecha}</td>
                      <td className="px-6 py-4">
                        <div className="space-y-1">
                          {v.detalles?.map((d, i) => (
                            <div key={i} className="text-xs text-slate-600">
                              • {d.producto?.nombre} x {d.cantidad} (₲ {Number(d.precio_unitario).toLocaleString('es-PY')})
                            </div>
                          ))}
                        </div>
                      </td>
                      <td className="px-6 py-4 font-bold text-[#2E7D32] text-base">
                        ₲ {Number(v.total).toLocaleString('es-PY')}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>
      </div>

      {/* New Venta Modal */}
      {isModalOpen && (
        <div className="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-50">
          <div className="bg-white border border-emerald-100 rounded-2xl w-full max-w-2xl p-6 shadow-2xl">
            <div className="flex items-center justify-between pb-4 border-b border-emerald-100">
              <h3 className="text-lg font-bold text-slate-800 flex items-center gap-2">
                <ShoppingCart className="w-5 h-5 text-[#2E7D32]" />
                Nueva Venta (Descuento Automático FIFO)
              </h3>
              <button onClick={() => setIsModalOpen(false)} className="text-slate-400 hover:text-slate-600">
                <X className="w-5 h-5" />
              </button>
            </div>

            <form onSubmit={handleSubmit} className="mt-6 space-y-6">
              <div>
                <label className="block text-xs font-bold text-slate-700 uppercase mb-1">Cliente</label>
                <select
                  value={clienteId}
                  onChange={(e) => setClienteId(e.target.value)}
                  className="w-full px-3.5 py-2.5 bg-slate-50 border border-emerald-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-[#2E7D32]"
                >
                  <option value="">-- Cliente Ocasional / Seleccionar --</option>
                  {clientes.map(c => (
                    <option key={c.id} value={c.id}>{c.nombre} (RUC: {c.ruc || 'N/A'})</option>
                  ))}
                </select>
              </div>

              <div>
                <div className="flex items-center justify-between mb-2">
                  <label className="text-xs font-bold text-slate-700 uppercase">Productos a Vender</label>
                  <button
                    type="button"
                    onClick={addDetalleRow}
                    className="text-xs text-[#2E7D32] hover:underline font-bold"
                  >
                    + Agregar Item
                  </button>
                </div>

                <div className="space-y-3">
                  {detalles.map((det, idx) => (
                    <div key={idx} className="flex items-center gap-3 bg-[#F1F8E9]/50 p-3 rounded-xl border border-emerald-100">
                      <select
                        required
                        value={det.producto_id}
                        onChange={(e) => handleDetalleChange(idx, 'producto_id', e.target.value)}
                        className="flex-1 px-3 py-2 bg-white border border-emerald-200 rounded-lg text-slate-800 text-xs focus:outline-none focus:border-[#2E7D32]"
                      >
                        <option value="">-- Seleccionar Producto --</option>
                        {productos.map(p => (
                          <option key={p.id} value={p.id}>{p.nombre} (Stock: {p.stock_total || 0} {p.unidad_medida})</option>
                        ))}
                      </select>

                      <input
                        type="number"
                        min="1"
                        placeholder="Cant."
                        required
                        value={det.cantidad}
                        onChange={(e) => handleDetalleChange(idx, 'cantidad', e.target.value)}
                        className="w-20 px-3 py-2 bg-white border border-emerald-200 rounded-lg text-slate-800 text-xs text-center"
                      />

                      <input
                        type="number"
                        min="0"
                        placeholder="Precio U."
                        required
                        value={det.precio_unitario}
                        onChange={(e) => handleDetalleChange(idx, 'precio_unitario', e.target.value)}
                        className="w-28 px-3 py-2 bg-white border border-emerald-200 rounded-lg text-slate-800 text-xs"
                      />

                      {detalles.length > 1 && (
                        <button
                          type="button"
                          onClick={() => removeDetalleRow(idx)}
                          className="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"
                        >
                          <Trash2 className="w-4 h-4" />
                        </button>
                      )}
                    </div>
                  ))}
                </div>
              </div>

              <div className="pt-4 border-t border-emerald-100 flex justify-end gap-3">
                <button
                  type="button"
                  onClick={() => setIsModalOpen(false)}
                  className="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-medium"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="px-4 py-2 bg-[#2E7D32] hover:bg-[#1b5e20] text-white rounded-xl text-sm font-medium shadow-md"
                >
                  Finalizar Venta
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </Layout>
  );
};

export default Ventas;
