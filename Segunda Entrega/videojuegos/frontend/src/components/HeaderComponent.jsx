import styles from "../assets/styles/Header.module.css"

import logo from '../assets/images/logo.png';

export function HeaderComponent(){
  return(
    <header id={styles.header}>
      <img src={logo} alt="logo" id={styles.logo}/>
      <a id={styles.titulo} href="/juegos">Videojuegos</a>
    </header>
  );
}