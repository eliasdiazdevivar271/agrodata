import React, { useEffect, useState } from 'react';
import Layout from '../components/Layout';
import api from '../api/axios';
import { useAuth } from '../context/AuthContext';
import { Plus, Search, Edit2, Trash2, X, Package } from 'lucide-react';
import toast from 'react-hot-toast';

const Productos = () => {
  const [productos, setProductos] = useState([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingProducto, setEditingProducto] = useState(null);
  const [formData, setFormData] = useState({
    nombre: '',
    categoria: '',
    unidad_medida: 'kg',
    stock_minimo: 10,
  });

  const { isAdmin } = useAuth();

  useEffect(() => {
    fetchProductos();
  }, [search]);

  const fetchProductos = async () => {
    try {
      const res = await api.get('/productos', {
        params: { search: search || undefined }
      });
      setProductos(res.data.data);
    } catch (err) {
      console.error('Error al cargar productos', err);
      toast.error('Error al cargar productos');
    } finally {
      setLoading(false);
    }
  };

  const handleOpenModal = (producto = null) => {
    if (producto) {
      setEditingProducto(producto);
      setFormData({
        nombre: producto.nombre,
        categoria: producto.categoria || '',
        unidad_medida: producto.unidad_medida,
        stock_minimo: producto.stock_minimo || 0,
      });
    } else {
      setEditingProducto(null);
      setFormData({
        nombre: '',
        categoria: '',
        unidad_medida: 'kg',
        stock_minimo: 10,
      });
    }
    setIsModalOpen(true);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (editingProducto) {
        await api.put(`/productos/${editingProducto.id}`, formData);
        toast.success('Producto actualizado exitosamente');
      } else {
        await api.post('/productos', formData);
        toast.success('Producto creado exitosamente');
      }
      setIsModalOpen(false);
      fetchProductos();
    } catch (err) {
      toast.error(err.response?.data?.message || 'Error al guardar el producto');
    }
  };

  const handleDelete = async (id) => {
    if (window.confirm('¿Está seguro de eliminar este producto?')) {
      try {
        await api.delete(`/productos/${id}`);
        toast.success('Producto eliminado');
        fetchProductos();
      } catch (err) {
        toast.error(err.response?.data?.message || 'No tiene permisos para eliminar este producto');
      }
    }
  };

  return (
    <Layout title="Gestión de Productos">
      <div className="space-y-6">
        {/* Actions header */}
        <div className="flex flex-col sm:flex-row items-center justify-between gap-4">
          <div className="relative w-full sm:w-80">
            <Search className="w-5 h-5 absolute left-3.5 top-3 text-slate-400" />
            <input
              type="text"
              placeholder="Buscar producto o categoría..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full pl-11 pr-4 py-2.5 bg-white border border-emerald-200 rounded-xl text-slate-800 placeholder-slate-400 text-sm focus:outline-none focus:border-[#2E7D32]"
            />
          </div>

          <button
            onClick={() => handleOpenModal()}
            className="flex items-center gap-2 px-4 py-2.5 bg-[#2E7D32] hover:bg-[#1b5e20] text-white font-medium rounded-xl text-sm transition-all shadow-md shadow-emerald-900/20 w-full sm:w-auto justify-center"
          >
            <Plus className="w-4 h-4" />
            Nuevo Producto
          </button>
        </div>

        {/* Product Table */}
        <div className="bg-white border border-emerald-100 rounded-2xl overflow-hidden shadow-sm">
          {loading ? (
            <div className="p-8 text-center text-slate-500 font-medium">Cargando productos...</div>
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full text-left text-sm text-slate-700">
                <thead className="bg-[#F1F8E9] text-[#2E7D32] font-bold uppercase text-xs border-b border-emerald-100">
                  <tr>
                    <th className="px-6 py-4">ID</th>
                    <th className="px-6 py-4">Nombre</th>
                    <th className="px-6 py-4">Categoría</th>
                    <th className="px-6 py-4">Stock Total</th>
                    <th className="px-6 py-4">Stock Mínimo</th>
                    <th className="px-6 py-4">Unidad</th>
                    <th className="px-6 py-4 text-right">Acciones</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-emerald-50">
                  {productos.map((p) => (
                    <tr key={p.id} className="hover:bg-[#F1F8E9]/40 transition-colors">
                      <td className="px-6 py-4 text-slate-400 font-mono">#{p.id}</td>
                      <td className="px-6 py-4 font-semibold text-slate-800">{p.nombre}</td>
                      <td className="px-6 py-4">
                        <span className="bg-[#F1F8E9] text-[#2E7D32] font-semibold border border-emerald-200 px-2.5 py-1 rounded-full text-xs">
                          {p.categoria || 'Sin Categoría'}
                        </span>
                      </td>
                      <td className="px-6 py-4 font-bold text-[#2E7D32]">
                        {p.stock_total || 0}
                      </td>
                      <td className="px-6 py-4 text-slate-500">{p.stock_minimo}</td>
                      <td className="px-6 py-4 text-slate-500">{p.unidad_medida}</td>
                      <td className="px-6 py-4 text-right">
                        <div className="flex items-center justify-end gap-2">
                          <button
                            onClick={() => handleOpenModal(p)}
                            className="p-1.5 text-slate-500 hover:text-[#2E7D32] hover:bg-emerald-50 rounded-lg transition-colors"
                            title="Editar"
                          >
                            <Edit2 className="w-4 h-4" />
                          </button>
                          {isAdmin && (
                            <button
                              onClick={() => handleDelete(p.id)}
                              className="p-1.5 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                              title="Eliminar"
                            >
                              <Trash2 className="w-4 h-4" />
                            </button>
                          )}
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>
      </div>

      {/* Create / Edit Modal */}
      {isModalOpen && (
        <div className="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-50">
          <div className="bg-white border border-emerald-100 rounded-2xl w-full max-w-md p-6 shadow-2xl">
            <div className="flex items-center justify-between pb-4 border-b border-emerald-100">
              <h3 className="text-lg font-bold text-slate-800 flex items-center gap-2">
                <Package className="w-5 h-5 text-[#2E7D32]" />
                {editingProducto ? 'Editar Producto' : 'Nuevo Producto'}
              </h3>
              <button
                onClick={() => setIsModalOpen(false)}
                className="text-slate-400 hover:text-slate-600"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            <form onSubmit={handleSubmit} className="mt-6 space-y-4">
              <div>
                <label className="block text-xs font-bold text-slate-700 uppercase mb-1">Nombre</label>
                <input
                  type="text"
                  required
                  value={formData.nombre}
                  onChange={(e) => setFormData({ ...formData, nombre: e.target.value })}
                  className="w-full px-3.5 py-2.5 bg-slate-50 border border-emerald-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-[#2E7D32]"
                  placeholder="Ej: Semilla de Maíz Híbrido"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-700 uppercase mb-1">Categoría</label>
                <input
                  type="text"
                  value={formData.categoria}
                  onChange={(e) => setFormData({ ...formData, categoria: e.target.value })}
                  className="w-full px-3.5 py-2.5 bg-slate-50 border border-emerald-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-[#2E7D32]"
                  placeholder="Ej: Semillas, Fertilizantes, Agroquímicos"
                />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-bold text-slate-700 uppercase mb-1">Unidad Medida</label>
                  <select
                    value={formData.unidad_medida}
                    onChange={(e) => setFormData({ ...formData, unidad_medida: e.target.value })}
                    className="w-full px-3.5 py-2.5 bg-slate-50 border border-emerald-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-[#2E7D32]"
                  >
                    <option value="kg">kg</option>
                    <option value="litros">litros</option>
                    <option value="unidades">unidades</option>
                    <option value="bolsas">bolsas</option>
                    <option value="toneladas">toneladas</option>
                  </select>
                </div>

                <div>
                  <label className="block text-xs font-bold text-slate-700 uppercase mb-1">Stock Mínimo</label>
                  <input
                    type="number"
                    min="0"
                    value={formData.stock_minimo}
                    onChange={(e) => setFormData({ ...formData, stock_minimo: parseInt(e.target.value) || 0 })}
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
                  {editingProducto ? 'Actualizar' : 'Guardar'}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </Layout>
  );
};

export default Productos;
