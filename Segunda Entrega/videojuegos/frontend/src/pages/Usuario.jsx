import Cookies from "cookies-js";
import { useEffect, useState } from "react";
import style from "../assets/styles/Usuario.module.css";
import { useAuth } from "../config/auth";
import { useNavigate } from "react-router-dom";

const recibirData = async (url) => {
  const token = Cookies.get("token");
  const resp = await fetch(url, {
    method: "GET",
    headers: {
      "Authorization": `Bearer ${token}`,
      "Content-Type": "application/json",
    },
  });

  const responseData = await resp.json();
  console.log(responseData);
  return responseData;
};

export default function Usuario() {
  const [userId, setUserId] = useState(null);
  const [calificaciones, setCalificaciones] = useState(null);
  const [errorMessage, setErrorMessage] = useState("");
  const [juegos, setJuegos] = useState([]);
  const { isLoggedIn, logout } = useAuth();
  const navigate = useNavigate();
  const [nombreUsuario, setNombreUsuario] = useState("");


  useEffect(() => {
      const userIdCookies = Cookies.get("id");
      const a = Cookies.get('a')
      setNombreUsuario(Cookies.get('nombre_usuario'))
      console.log(a)
      if (userIdCookies) setUserId(userIdCookies);
  }, [isLoggedIn]);

  useEffect(() => {
    if(userId){
      var obtenerReseñas = async () => {
        const respuestaJson = await recibirData(
          `http://localhost/calificaciones/${userId}`
        );
        
        
        if (respuestaJson.Codigo === 200) {
          setCalificaciones(respuestaJson.Data);
        } else {
          setErrorMessage(respuestaJson.Mensaje);
          
        }
      };
      obtenerReseñas();
      }
  }, [userId]);

  useEffect(() => {
    if (calificaciones) {
      var obtenerJuegos = async () => {
        const respuestaJson = await recibirData(
          `http://localhost/juegosCompletos`
        );

        if (respuestaJson.Codigo === 200) {
          setJuegos(respuestaJson.Data.juegos);
        } else {
          setErrorMessage(respuestaJson.Mensaje);
        }
      };
      obtenerJuegos();
    }
  }, [calificaciones]);

  return (
    <div className={style.contenedorUsuario}>
      {isLoggedIn ? (
        <div id={style.usuario}>
          <div id={style.titulos}>
          <h2>Tus calificaciones</h2>
          <h3 id={style.usuarioNombre}>{nombreUsuario}</h3>
          </div>
          <div id={style.contenedorCalificaciones}>
            {calificaciones && calificaciones.length > 0 ? (
              <ul id={style.calificaciones}>
                {calificaciones
                  .sort((a, b) => b.estrellas - a.estrellas)
                  .map((c) => {
                    const juego = juegos.find((j) => j.id === c.juego_id);
                    const nombreJuego = juego ? juego.nombre : "";
                    return (
                      <li key={c.id}>
                        <span id={style.estrellasUsuario}>
                          {c.estrellas} estrellas
                        </span>
                        , {nombreJuego}
                      </li>
                    );
                  })}
              </ul>
            ) : (
              <p>No tienes calificaciones hechas</p>
            )}
          </div>
        </div>
      ) : (
        <div>{navigate("/juegos")}</div>
      )}
    </div>
  );
}
