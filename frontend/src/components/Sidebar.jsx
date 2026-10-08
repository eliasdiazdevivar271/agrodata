import React from 'react';
import { NavLink } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { 
  LayoutDashboard, 
  Package, 
  Boxes, 
  ArrowLeftRight, 
  ShoppingCart, 
  ShoppingBag, 
  Users, 
  BarChart3, 
  LogOut,
  Sprout,
  Truck
} from 'lucide-react';

const Sidebar = () => {
  const { user, logout, isAdmin } = useAuth();

  const navItems = [
    { name: 'Dashboard', path: '/dashboard', icon: LayoutDashboard },
    { name: 'Productos', path: '/productos', icon: Package },
    { name: 'Lotes', path: '/lotes', icon: Boxes },
    { name: 'Movimientos', path: '/movimientos', icon: ArrowLeftRight },
    { name: 'Ventas', path: '/ventas', icon: ShoppingCart },
    { name: 'Clientes', path: '/clientes', icon: Users },
    { name: 'Proveedores', path: '/proveedores', icon: Truck },
  ];

  if (isAdmin) {
    navItems.push(
      { name: 'Compras', path: '/compras', icon: ShoppingBag },
      { name: 'Reportes', path: '/reportes', icon: BarChart3 }
    );
  }

  return (
    <aside className="w-64 bg-white border-r border-emerald-100 flex flex-col justify-between p-4 min-h-screen shadow-sm">
      <div>
        <div className="flex items-center gap-3 px-3 py-4 mb-6 border-b border-emerald-100">
          <div className="bg-[#2E7D32] text-white p-2.5 rounded-xl shadow-md">
            <Sprout className="w-7 h-7" />
          </div>
          <div>
            <h1 className="font-bold text-xl text-[#2E7D32] tracking-tight">AgroData</h1>
            <span className="text-xs text-[#2E7D32] font-semibold bg-[#F1F8E9] border border-emerald-200 px-2 py-0.5 rounded-full">
              Pymes Paraguay
            </span>
          </div>
        </div>

        <nav className="space-y-1.5">
          {navItems.map((item) => {
            const Icon = item.icon;
            return (
              <NavLink
                key={item.path}
                to={item.path}
                className={({ isActive }) =>
                  `flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all ${
                    isActive
                      ? 'bg-[#2E7D32] text-white shadow-md shadow-emerald-900/20'
                      : 'text-slate-600 hover:text-[#2E7D32] hover:bg-[#F1F8E9]'
                  }`
                }
              >
                <Icon className="w-5 h-5" />
                {item.name}
              </NavLink>
            );
          })}
        </nav>
      </div>

      <div className="pt-4 border-t border-emerald-100">
        <div className="flex items-center justify-between px-3 py-2 bg-[#F1F8E9] rounded-xl border border-emerald-200">
          <div className="truncate">
            <p className="text-sm font-bold text-slate-800 truncate">{user?.name}</p>
            <p className="text-xs text-[#2E7D32] font-medium capitalize">{user?.rol || 'operador'}</p>
          </div>
          <button
            onClick={logout}
            className="p-2 text-slate-500 hover:text-red-600 hover:bg-red-100/50 rounded-lg transition-colors"
            title="Cerrar sesión"
          >
            <LogOut className="w-5 h-5" />
          </button>
        </div>
      </div>
    </aside>
  );
};

export default Sidebar;
