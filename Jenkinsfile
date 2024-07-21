pipeline {
    agent any

    environment {
        DOCKER_HUB_CREDENTIALS = credentials('docker-hub-credentials')
        START_SCRIPTS_PATH = credentials('start-scripts-path')
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Copy Scripts') {
            steps {
                script {
                    // Copy keyfile.json and start.sh to the working directory
                    sh """
                        cp ${env.START_SCRIPTS_PATH}/keyfile.json .
                        cp ${env.START_SCRIPTS_PATH}/start.sh .
                    """
                }
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
