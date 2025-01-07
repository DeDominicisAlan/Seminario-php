import { RouterConfig } from "./config/RouterConfig";
import { NavbarComponent } from "./components/NavbarComponent";
import { FooterComponent } from "./components/FooterComponent";
import { AuthProvider } from "./config/auth";
import Preload from "./components/LoadComponent";
import "./App.css";

function App() {
  return (
    <div className="App">
      <AuthProvider>
        <Preload />
        <NavbarComponent />
        <RouterConfig />
        <FooterComponent />
      </AuthProvider>
    </div>
  );
}

export default App;
