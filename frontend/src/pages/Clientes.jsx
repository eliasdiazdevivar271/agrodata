import React, { useEffect, useState } from 'react';
import Layout from '../components/Layout';
import api from '../api/axios';
import { Users, Plus, X } from 'lucide-react';
import toast from 'react-hot-toast';

const Clientes = () => {
  const [clientes, setClientes] = useState([]);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [formData, setFormData] = useState({ nombre: '', ruc: '', telefono: '', email: '', direccion: '' });

  useEffect(() => {
    fetchClientes();
  }, []);

  const fetchClientes = async () => {
    try {
      const res = await api.get('/clientes');
      setClientes(res.data.data);
    } catch (err) {
      console.error('Error al cargar clientes', err);
      toast.error('Error al cargar clientes');
    } finally {
      setLoading(false);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await api.post('/clientes', formData);
      toast.success('Cliente registrado exitosamente');
      setIsModalOpen(false);
      setFormData({ nombre: '', ruc: '', telefono: '', email: '', direccion: '' });
      fetchClientes();
    } catch (err) {
      toast.error(err.response?.data?.message || 'Error al guardar cliente');
    }
  };

  return (
    <Layout title="Gestión de Clientes">
      <div className="space-y-6">
        <div className="flex justify-end">
          <button
            onClick={() => setIsModalOpen(true)}
            className="flex items-center gap-2 px-4 py-2.5 bg-[#2E7D32] hover:bg-[#1b5e20] text-white font-medium rounded-xl text-sm transition-all shadow-md shadow-emerald-900/20"
          >
            <Plus className="w-4 h-4" />
            Nuevo Cliente
          </button>
        </div>

        <div className="bg-white border border-emerald-100 rounded-2xl overflow-hidden shadow-sm">
          {loading ? (
            <div className="p-8 text-center text-slate-500 font-medium">Cargando clientes...</div>
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full text-left text-sm text-slate-700">
                <thead className="bg-[#F1F8E9] text-[#2E7D32] font-bold uppercase text-xs border-b border-emerald-100">
                  <tr>
                    <th className="px-6 py-4">ID</th>
                    <th className="px-6 py-4">Nombre / Empresa</th>
                    <th className="px-6 py-4">RUC</th>
                    <th className="px-6 py-4">Teléfono</th>
                    <th className="px-6 py-4">Email</th>
                    <th className="px-6 py-4">Dirección</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-emerald-50">
                  {clientes.map((c) => (
                    <tr key={c.id} className="hover:bg-[#F1F8E9]/40 transition-colors">
                      <td className="px-6 py-4 text-slate-400 font-mono">#{c.id}</td>
                      <td className="px-6 py-4 font-semibold text-slate-800">{c.nombre}</td>
                      <td className="px-6 py-4 font-mono text-[#2E7D32] font-semibold">{c.ruc || 'N/A'}</td>
                      <td className="px-6 py-4 text-slate-500">{c.telefono || 'N/A'}</td>
                      <td className="px-6 py-4 text-slate-500">{c.email || 'N/A'}</td>
                      <td className="px-6 py-4 text-slate-500">{c.direccion || 'N/A'}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>
      </div>

      {isModalOpen && (
        <div className="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-50">
          <div className="bg-white border border-emerald-100 rounded-2xl w-full max-w-md p-6 shadow-2xl">
            <div className="flex items-center justify-between pb-4 border-b border-emerald-100">
              <h3 className="text-lg font-bold text-slate-800 flex items-center gap-2">
                <Users className="w-5 h-5 text-[#2E7D32]" />
                Nuevo Cliente
              </h3>
              <button onClick={() => setIsModalOpen(false)} className="text-slate-400 hover:text-slate-600">
                <X className="w-5 h-5" />
              </button>
            </div>

            <form onSubmit={handleSubmit} className="mt-6 space-y-4">
              <div>
                <label className="block text-xs font-bold text-slate-700 uppercase mb-1">Nombre / Razón Social</label>
                <input
                  type="text"
                  required
                  value={formData.nombre}
                  onChange={(e) => setFormData({ ...formData, nombre: e.target.value })}
                  className="w-full px-3.5 py-2.5 bg-slate-50 border border-emerald-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-[#2E7D32]"
                />
              </div>
              <div>
                <label className="block text-xs font-bold text-slate-700 uppercase mb-1">RUC</label>
                <input
                  type="text"
                  value={formData.ruc}
                  onChange={(e) => setFormData({ ...formData, ruc: e.target.value })}
                  className="w-full px-3.5 py-2.5 bg-slate-50 border border-emerald-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-[#2E7D32]"
                />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-bold text-slate-700 uppercase mb-1">Teléfono</label>
                  <input
                    type="text"
                    value={formData.telefono}
                    onChange={(e) => setFormData({ ...formData, telefono: e.target.value })}
                    className="w-full px-3.5 py-2.5 bg-slate-50 border border-emerald-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-[#2E7D32]"
                  />
                </div>
                <div>
                  <label className="block text-xs font-bold text-slate-700 uppercase mb-1">Email</label>
                  <input
                    type="email"
                    value={formData.email}
                    onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                    className="w-full px-3.5 py-2.5 bg-slate-50 border border-emerald-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-[#2E7D32]"
                  />
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
                  Guardar
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </Layout>
  );
};

export default Clientes;
