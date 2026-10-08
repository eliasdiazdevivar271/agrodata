import React from 'react';
import { useAuth } from '../context/AuthContext';
import { User, ShieldCheck, LogOut } from 'lucide-react';

const Header = ({ title }) => {
  const { user, logout } = useAuth();

  return (
    <header className="bg-white border-b border-emerald-100 px-8 py-4 flex items-center justify-between shadow-sm">
      <div>
        <h2 className="text-2xl font-bold text-[#2E7D32]">{title}</h2>
      </div>

      <div className="flex items-center gap-4">
        <div className="flex items-center gap-3 bg-[#F1F8E9] px-4 py-2 rounded-xl border border-emerald-200">
          <div className="bg-[#2E7D32] text-white p-1.5 rounded-lg">
            <User className="w-5 h-5" />
          </div>
          <div>
            <p className="text-sm font-semibold text-slate-800">{user?.name}</p>
            <div className="flex items-center gap-1 text-xs text-[#2E7D32] font-medium">
              <ShieldCheck className="w-3.5 h-3.5" />
              <span className="capitalize">{user?.rol || 'operador'}</span>
            </div>
          </div>
        </div>

        <button
          onClick={logout}
          className="flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-xl transition-colors font-medium border border-red-200"
          title="Cerrar sesión"
        >
          <LogOut className="w-4 h-4" />
          Salir
        </button>
      </div>
    </header>
  );
};

export default Header;
