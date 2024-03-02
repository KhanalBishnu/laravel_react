import "bootstrap/dist/css/bootstrap.min.css";
import AuthUser from "./AuthUser";
import Guest from "./view/Guest";
import Auth from "./view/Auth";
function App() {
  const {getToken}=AuthUser();
  if(!getToken()){
    return <Guest />
  }
  return (
   <Auth />
  );
    
}

export default App;
