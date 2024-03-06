import React, { useEffect, useState } from "react";
import AuthUser from "../AuthUser";

function UserList() {
  const [users, setUsers] = useState([]);
  const [successMessage, setSuccessMessage] = useState(null);
  const [errorMessage, setErrorMessage] = useState(null);
  const [editUserId, seteditUserId] = useState(null);
  const [showModal, setShowModal] = useState(false);

  const [name, setName] = useState("");
  const [email, setEmail] = useState("");

  const { http } = AuthUser();
  useEffect(() => {
    getAllUser();
    if (showModal) {
        const modal = new bootstrap.Modal(
          document.getElementById(`EditUserModal${editUserId}`)
        );
        modal.show();
        getUserData(editUserId);
      }
  }, [users]);
  const getAllUser = () => {
    http.get("/users").then((res) => {
      if (res.data.response) {
        setUsers(res.data.users);
      }
    });
  };
  const deleteUser = (userId) => {
    http.get(`/user/delete/${userId}`).then((res) => {
      console.log(res.data);
      if (res.data.response) {
        setSuccessMessage(res.data.message);
        setErrorMessage(null);
      } else {
        setErrorMessage(res.data.message);
        setSuccessMessage(null);
      }
    });
  };

  const handleEditFunction = (userID) => {
    seteditUserId(userID);
    setShowModal(true);
  };

  const getUserData = (editUserId) => {
    http.get(`/user/edit/${editUserId}`).then((res) => {
      if (res.data.response) {
        setEmail(res.data.user.email);
        setName(res.data.user.name);
      } else {
        setErrorMessage(res.data.message);
      }
    });
  };
  return (
    <div className="container">
      <div className="row">
        {successMessage && (
          <h6 className="bg-success text-center text-white p-2 mt-3">
            {successMessage}
          </h6>
        )}
        {errorMessage && (
          <h6 className="bg-danger p-2 text-center text-white mt-3">
            {errorMessage}
          </h6>
        )}
        <h6>Users List </h6>
        <table className="table table-bordered ">
          <thead>
            <tr>
              <th>SN</th>
              <th>Name</th>
              <th>Email</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            {users.map((user, index) => (
              <tr key={user.id}>
                <td>{++index}</td>
                <td>{user.name}</td>
                <td>{user.email}</td>
                <td>
                  <button
                    className="btn btn-primary mx-1"
                    onClick={() => handleEditFunction(user.id)}
                  >
                    Edit
                  </button>
                  <button
                    className="btn btn-danger"
                    onClick={() => deleteUser(user.id)}
                  >
                    Delete
                  </button>
                </td>
              </tr>
            ))}
            ;
          </tbody>
        </table>
      </div>

      <div
        className="modal fade"
        id={`EditUserModal${editUserId}`}
        tabIndex="-1"
        aria-labelledby="EditUserModalLabel"
        aria-hidden="true"
      >
        <div className="modal-dialog">
          <div className="modal-content">
            <div className="modal-header">
              <h5 className="modal-title" id="EditUserModalLabel">
                Edit User Form
              </h5>
              <button
                type="button"
                className="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
              ></button>
            </div>
            <div className="modal-body">
              <div className="container">
                <div className="row ">
                  <div className="col-md-12">
                    <label htmlFor="">
                      Name<sup className="text-danger">*</sup>
                    </label>
                    <input
                      type="text"
                      name="name"
                      className="form-control"
                      value={name}
                    />
                  </div>
                  <div className="col-md-12 mb-1">
                    <label htmlFor="">
                      Email <sup className="text-danger">*</sup>
                    </label>
                    <input
                      type="email"
                      name="email"
                      className="form-control "
                      value={email}
                    />
                  </div>
                </div>
              </div>
            </div>
            <div className="modal-footer">
              <button
                type="button"
                className="btn btn-secondary"
                data-bs-dismiss="modal"
                onClick={() => setShowModal(false)}
              >
                Close
              </button>
              <button type="button" className="btn btn-primary">
                Save
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default UserList;
