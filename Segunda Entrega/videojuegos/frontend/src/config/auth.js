import React, { createContext, useContext, useEffect, useState } from "react";
import Cookies from "cookies-js";
import { jwtDecode } from "jwt-decode";

const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const [isLoggedIn, setIsLoggedIn] = useState(false);

  useEffect(() => {
    const token = Cookies.get("token");
    if (token) {
      const decodedToken = jwtDecode(token);
      const currentTime = Date.now() / 1000;

      if (decodedToken.exp > currentTime) {
        setIsLoggedIn(true);
      } else logout();
    }
  }, []);

  const login = (token) => {
    Cookies.set("token", token);
    setIsLoggedIn(true);
  };

  const logout = () => {
    Cookies.set("token", "", { expires: -1 });
    setIsLoggedIn(false);
  };

  return (
    <AuthContext.Provider value={{ isLoggedIn, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  return useContext(AuthContext);
};
