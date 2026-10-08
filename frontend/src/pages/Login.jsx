import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { Sprout, Lock, Mail, AlertCircle } from 'lucide-react';
import toast from 'react-hot-toast';

const Login = () => {
  const [email, setEmail] = useState('admin@agrodata.com');
  const [password, setPassword] = useState('password');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const { login } = useAuth();
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      await login(email, password);
      toast.success('¡Bienvenido a AgroData!');
      navigate('/dashboard');
    } catch (err) {
      const msg = err.response?.data?.message || 'Credenciales inválidas. Por favor intente de nuevo.';
      setError(msg);
      toast.error(msg);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-[#F1F8E9] flex items-center justify-center p-4">
      <div className="max-w-md w-full bg-white border border-emerald-100 rounded-3xl p-8 shadow-xl">
        <div className="text-center mb-8">
          <div className="inline-flex items-center justify-center w-16 h-16 bg-[#2E7D32] text-white rounded-2xl mb-4 shadow-md">
            <Sprout className="w-10 h-10" />
          </div>
          <h1 className="text-3xl font-bold text-[#2E7D32] tracking-tight">AgroData</h1>
          <p className="text-slate-500 text-sm mt-2 font-medium">Plataforma de Gestión Agrónoma para PYMEs</p>
        </div>

        {error && (
          <div className="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3 text-red-600 text-sm">
            <AlertCircle className="w-5 h-5 shrink-0" />
            <span>{error}</span>
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-5">
          <div>
            <label className="block text-xs font-bold text-slate-700 uppercase mb-2">Correo Electrónico</label>
            <div className="relative">
              <Mail className="w-5 h-5 absolute left-3.5 top-3.5 text-slate-400" />
              <input
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
                className="w-full pl-11 pr-4 py-3 bg-[#F1F8E9]/50 border border-emerald-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-[#2E7D32] focus:ring-1 focus:ring-[#2E7D32] transition-all text-sm font-medium"
                placeholder="admin@agrodata.com"
              />
            </div>
          </div>

          <div>
            <label className="block text-xs font-bold text-slate-700 uppercase mb-2">Contraseña</label>
            <div className="relative">
              <Lock className="w-5 h-5 absolute left-3.5 top-3.5 text-slate-400" />
              <input
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                required
                className="w-full pl-11 pr-4 py-3 bg-[#F1F8E9]/50 border border-emerald-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-[#2E7D32] focus:ring-1 focus:ring-[#2E7D32] transition-all text-sm font-medium"
                placeholder="••••••••"
              />
            </div>
          </div>

          <button
            type="submit"
            disabled={loading}
            className="w-full py-3.5 px-4 bg-[#2E7D32] hover:bg-[#1b5e20] text-white font-semibold rounded-xl shadow-lg shadow-emerald-900/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed mt-2 text-sm"
          >
            {loading ? 'Iniciando sesión...' : 'Iniciar Sesión'}
          </button>
        </form>

        <div className="mt-8 pt-6 border-t border-emerald-100 text-center text-xs text-slate-500">
          Credenciales demo: <span className="text-[#2E7D32] font-mono font-bold">admin@agrodata.com</span> / <span className="text-[#2E7D32] font-mono font-bold">password</span>
        </div>
      </div>
    </div>
  );
};

export default Login;
