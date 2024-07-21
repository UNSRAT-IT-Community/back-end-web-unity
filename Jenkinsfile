pipeline {
    agent any

    environment {
        DOCKER_HUB_CREDENTIALS = credentials('docker-hub-credentials')
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Build') {
            steps {
                script {
                    def backendImage = docker.build("backend-webunity:latest")
                }
            }
        }

        stage('Deploy') {
            steps {
                script {
                    sh 'docker-compose up -d --no-deps --build backend-webunity'
                }
            }
        }

        stage('Verify') {
            steps {
                script {
                    sh 'docker-compose logs backend-webunity'
                }
            }
        }
    }
}
