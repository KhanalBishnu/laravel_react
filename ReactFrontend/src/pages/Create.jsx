import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import AuthUser from "../AuthUser";

function Create() {
  const [errors,setErrors] =useState([]);
  const [successMessage,setSuccessMessage] =useState();
  const {http}=AuthUser();
  const navigate = useNavigate();
  const [inputs, setInputs] = useState({});
  const handleChange = (event) => {
    const name = event.target.name;
    const value = event.target.value;
    setInputs((values) => ({ ...values, [name]: value }));
  };
  const submitUserForm = () => {
    http.post('/user/create',inputs).then((res)=>{
      if(res.data.response==false){
        setErrors(res.data.message);
        setSuccessMessage('');
      }else{
        setSuccessMessage(res.data.message);
        setErrors([]);

      }
    })
  };
  return (
    <div className="container mt-4">
      <div className="row justify-content-center ">
        <div className="col-md-6  border rounded p-2 ">
        { errors && Object.keys(errors).map((key) => (
            <h6 className="p-2 bg-danger text-white" key={key}>{errors[key][0]}</h6>
        ))}
            {successMessage && <h6 className="text-white p-2 bg-success ">{successMessage}</h6>}
        </div>
      </div>
      <div className="row justify-content-center">

        <div className="col-md-6">
          <div className="row border border-dark p-2 rounded ">
            <label className="p-2 bg-secondary text-info text-center">User Create Form</label>
            <div className="col-md-12">
              <label htmlFor="">Name</label>
              <input
                type="text"
                name="name"
                className="form-control"
                value={inputs.name || ""}
                onChange={handleChange}
              />
            </div>
            <div className="col-md-12">
              <label htmlFor="">Email</label>
              <input
                type="email"
                name="email"
                className="form-control"
                value={inputs.email || ""}
                onChange={handleChange}
              />
            </div>
            <div className="col-md-12">
              <label htmlFor="">Password</label>
              <input
                type="password"
                name="password"
                className="form-control"
                value={inputs.password || ""}
                onChange={handleChange}
              />
            </div>
            <div className="col-md-12">
              <button
                type="button"
                className="btn btn-primary mt-3"
                onClick={submitUserForm}
              >
                Submit
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default Create;
