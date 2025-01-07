import React, { useEffect, useRef, useState } from "react";
import styles from "../../assets/styles/Juegos.module.css";
import { useAuth } from "../../config/auth";
import Cookies from "cookies-js";
import { realizarSolicitud, BASE_URL } from "../../config/data";
import { imagenValida } from "../../config/validarImagen";

export default function Juegos(props) {
  const [juegos, setJuegos] = useState([]);
  const [tamaño, setTamaño] = useState(1);
  const [paginaActual, setPaginaActual] = useState(1);
  const [clasificacion, setClasificacion] = useState("");
  const [texto, setTexto] = useState("");
  const [plataforma, setPlataforma] = useState("");
  const [plataformasDisponibles, setPlataformasDisponibles] = useState([]); 
  const { isLoggedIn } = useAuth();
  const botonesRef = useRef([]);
  
  useEffect(() => {
    const obtenerPlataformas = async () => {
      const respuestaJson = await realizarSolicitud("`${BASE_URL}plataformas`");
      if (respuestaJson.Codigo === 200) {
        setPlataformasDisponibles(respuestaJson.Data.plataformas); 
      }
    };
    obtenerPlataformas();
  }, []);

  useEffect(() => {
    // Establecer el foco en el primer botón al cargar el componente asi aparece como predeterminado la pagina 1 (0)
    if (botonesRef.current[0]) {
      botonesRef.current[0].focus();
    }
  }, []);

  const actualizarUrl = () => {
    const queryParams = new URLSearchParams({
      pagina: paginaActual,
      clasificacion: clasificacion || "", // Añadir solo si hay clasificación
      texto: texto || "", // Añadir solo si hay texto
      plataforma: plataforma || "", // Añadir solo si hay plataforma
    }).toString();
    return `${BASE_URL}juegos?${queryParams}`;
  };

  const obtenerCalificacion = async (id) => {
    const id_usuario = Cookies.get("id");
    const respuestaJson = await realizarSolicitud(
      `${BASE_URL}calificacion?id_juego=${id}&id_usuario=${id_usuario}`
    );

    localStorage.setItem("id_juego", `${id}`);

    if (respuestaJson.Codigo === 200) {
      localStorage.setItem("calificacion", "true");
      localStorage.setItem("calificacionId", `${respuestaJson.Data.id}`);
    } else {
      localStorage.setItem("calificacion", "false");
    }
    setTimeout(() => {
      window.location.assign("/calificacion");
    }, 1000);
  };

  useEffect(() => {
    const url = actualizarUrl();
    var obtenerJuegos = async () => {
      const respuestaJson = await realizarSolicitud(url);

      if (respuestaJson.Codigo === 200) {
        setJuegos(respuestaJson.Data.juegos);
        setTamaño(Math.ceil(respuestaJson.Data.size / 5));
      }
    };
    obtenerJuegos();
  }, [paginaActual, clasificacion, texto, plataforma]);

  const handlePaginaChange = (newPage) => {
    if (newPage !== paginaActual) {
      setPaginaActual(newPage);
    }
  };

  const handleTextoChange = (e) => {
    setTexto(e.target.value);
    setPaginaActual(1);
  };

  const handleClasificacionChange = (e) => {
    setClasificacion(e.target.value);
    setPaginaActual(1);
  };

  const handlePlataformaChange = (e) => {
    setPlataforma(e.target.value);
    setPaginaActual(1);
  };

  //Lo de setPaginaActual es para que cuando se aplique un filtro, se devuelva siempre la primer pagina

  const verificarCalificacion = (calificacion) => {
    let c = Number.parseFloat(calificacion).toFixed(1);
    if (isNaN(c)) return "";
    return c;
  };

  const colorPuntuacion = (puntuacion) => {
    if (puntuacion >= 5) return styles.puntuacionVerde;
    if (puntuacion >= 3) return styles.puntuacionAmarilla;
    return styles.puntuacionRoja;
  };

  return (
    <div id={styles.containerJuegos}>
      <div id={styles.filtros}>
        <p>Busqueda</p>
        <input
          type="text"
          onChange={handleTextoChange}
          value={texto}
          placeholder="Buscar por nombre o caracter"
          name="busquedaNombre"
        />
        <p>Clasificación</p>
        <select onChange={handleClasificacionChange} value={clasificacion}
        name="busquedaClasificacion">
          <option value="">Todas</option>
          <option value="ATP">ATP</option>
          <option value="+13">+13</option>
          <option value="+18">+18</option>
        </select>
        <p>Plataforma</p>
        <select onChange={handlePlataformaChange} value={plataforma} name="busquedaPlataforma">
          <option value="">Todas</option>
          {plataformasDisponibles.map((plataforma) => (
            <option key={plataforma.id} value={plataforma.nombre}>
              {plataforma.nombre}
            </option>
          ))}
        </select>
      </div>

      <div id={styles.contenedorMain}>
        <ul id={styles.listaJuegos}>
          {juegos.map((juego) => (
            <li key={juego.id} id={styles.juego}>
            
              <img
                src={imagenValida("data:image/jpeg;base64," + juego.imagen)}
                alt="Imagen del juego"
                className={styles.img}
              />
              <div className={styles.overlay}>
             
                <a className={styles.juegoNombre} href={`juego/${juego.id}`}>{juego.nombre}</a>
                <p className={styles.juegoEdad}>{juego.clasificacion_edad}</p>
                <p id={styles.juegoRating} >
                  {verificarCalificacion(juego.puntuacion_promedio) !== "" ? <span className={colorPuntuacion(verificarCalificacion(juego.puntuacion_promedio))}>⭐{verificarCalificacion(juego.puntuacion_promedio)}</span> : "" }
                </p>
                <p className={styles.juegoPlataforma}>{juego.plataformas}</p>
                {isLoggedIn && (
                  <button
                    className={styles.botonCalificacion}
                    onClick={() => obtenerCalificacion(juego.id)}>
                    Puntuar
                  </button>
                )}
              </div>
            
          </li>
          
          ))}
        </ul>
        <div id={styles.botones}>
          {Array.from({ length: tamaño }).map((_, i) => (
            <button
              key={i}
              ref={(el) => (botonesRef.current[i] = el)}
              className={styles.boton}
              typeof="button"
              onClick={() => handlePaginaChange(i + 1)}>
              {i + 1}
            </button>
          ))}
        </div>
      </div>
    </div>
  );
}
