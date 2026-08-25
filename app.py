import streamlit as st
import requests

API_URL = "https://expense-tracker-api-w0c2.onrender.com"

st.set_page_config(page_title="Enterprise AI Gateway", page_icon="🤖")
st.title("🤖 Enterprise AI Gateway Dashboard")

# Prompt Input
prompt = st.text_area("Enter Prompt:")
if st.button("Send Request"):
    res = requests.post(f"{API_URL}/v1/chat/completions", params={"prompt": prompt})
    st.write(res.json())
