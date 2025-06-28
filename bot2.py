from flask import Flask, request, jsonify
from transformers import T5ForConditionalGeneration, T5Tokenizer
from flask_cors import CORS

# Initialize Flask app
app = Flask(__name__)
CORS(app)  # ✅ CORS applied after app is created

# Load model and tokenizer
model = T5ForConditionalGeneration.from_pretrained("./t5-cuib-faq-model")
tokenizer = T5Tokenizer.from_pretrained("t5-base")

@app.route("/ask", methods=["POST"])
def ask():
    data = request.get_json()
    question = data.get("question", "")
    
    if not question:
        return jsonify({"error": "No question provided"}), 400

    # Encode and generate answer
    input_text = "question: " + question
    input_ids = tokenizer.encode(input_text, return_tensors="pt", max_length=64, truncation=True)
    output_ids = model.generate(input_ids=input_ids, max_length=128, num_beams=2, early_stopping=True)
    answer = tokenizer.decode(output_ids[0], skip_special_tokens=True)

    return jsonify({"answer": answer})

if __name__ == "__main__":
    app.run(debug=True, port=5000)  # Explicitly set port
