pipeline {
    agent {
        label 'org.jenkinsci.plugins.docker.commons.tools.DockerTool'
    }
    environment {
        SOME_ENV_VAR = 'some_value'
    }
    tools {
        dockerTool 'Docker'
        // jdk 'your_jdk_version'
    }

    stages {
        stage('Checkout') {
            steps {
                git branch: 'main', url: 'https://github.com/Kiliroystrik/symfony_tests-cours.git'
            }
        }

        stage('Install') {
            steps {
                script {
                    // Installer les dépendances Composer
                    sh 'composer install --no-interaction --prefer-dist'
                }
            }
        }

        stage('Tests') {
            steps {
                script {
                    // Lancer PHPUnit
                    sh './vendor/bin/phpunit'
                }
            }
        }
    }
}
