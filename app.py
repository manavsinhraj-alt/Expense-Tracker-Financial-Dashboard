import streamlit as st
import requests

# Live Render API URL
API_URL = "https://expense-tracker-api-w0c2.onrender.com"

st.set_page_config(page_title="Expense Tracker", page_icon="💰")
st.title("💰 Expense Tracker & Financial Dashboard")

# Test Connection to Backend
try:
    response = requests.get(f"{API_URL}/")
    if response.status_code == 200:
        st.success("Connected to Backend API!")
    else:
        st.info("API connected.")
except Exception as e:
    st.error(f"Unable to connect to API: {e}")

# Frontend UI elements
st.subheader("Add New Expense")
amount = st.number_input("Amount", min_value=0.0, step=10.0)
category = st.selectbox("Category", ["Food", "Travel", "Bills", "Shopping", "Other"])

if st.button("Submit Expense"):
    st.write(f"Logged {category} expense of ₹{amount}")
