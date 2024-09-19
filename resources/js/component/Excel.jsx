import React, { useState } from 'react';
import ReactDom from 'react-dom/client';
import axios from 'axios';

export default function Excel() {
    const buttonStyle = {
        backgroundColor: "#4CAF50",
        border: "none",
        color: "white",
        padding: "10px 20px",
        textAlign: "center",
        textDecoration: "none",
        display: "inline-block",
        fontSize: "16px",
        margin: "10px 0",
        cursor: "pointer",
        borderRadius: "5px",
        transitionDuration: "0.4s"
    };

    const [selectedFile, setSelectedFile] = useState(null);


    const handleFileChange = (event) => {
        setSelectedFile(event.target.files[0]);
    };


    const handleSubmit = async (event) => {
        event.preventDefault();

        if (!selectedFile) {
            alert("Please select a file first!");
            return;
        }

        const formData = new FormData();
        formData.append('file', selectedFile);

        try {
            const response = await axios.post('/api/upload-excel', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
            // console.log("response",response)
            alert('File uploaded and processed successfully');
        } catch (error) {
            console.error("Error uploading file", error);
            alert('Error uploading file');
        }
    };


    return (
    <div>
        <h1> Upload Excel File </h1>
        <form onSubmit={handleSubmit}>
            <input type="file" accept=".xlsx, .csv" onChange={handleFileChange} />
            <button type="submit" style={buttonStyle}>Upload</button>
        </form>
    </div>


    );
}
const container = document.getElementById('app');
const root = ReactDom.createRoot(container);
root.render(<Excel/>);
