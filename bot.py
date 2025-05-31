import pandas as pd
from flask import Flask, request, jsonify
from flask_cors import CORS
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

# Load the dataset
faq_data = pd.read_csv("CUIB_FAQ_Dataset.csv")
questions = faq_data['Question']
answers = faq_data['Answer']

# Vectorize the questions using TF-IDF
vectorizer = TfidfVectorizer()
X = vectorizer.fit_transform(questions)

# Create a Flask app
app = Flask(__name__)
CORS(app)  # Enable CORS to allow frontend access

@app.route('/chat', methods=['POST'])
def chat():
    data = request.get_json()
    user_input = data.get('message')

    if not user_input:
        return jsonify({'response': "Please ask a question."})

    # Vectorize user input
    user_vec = vectorizer.transform([user_input])

    # Compute cosine similarity
    similarity = cosine_similarity(user_vec, X)
    best_match_idx = similarity.argmax()
    best_score = similarity[0, best_match_idx]

    # You can adjust the threshold to handle unrelated questions
    if best_score < 0.3:
        return jsonify({'response': "Sorry, I don't understand your question yet."})

    # Return the most relevant answer
    response = answers[best_match_idx]
    return jsonify({'response': response})

# Run the Flask app
if __name__ == "__main__":
    app.run(debug=True)