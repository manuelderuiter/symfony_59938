#!/bin/bash

# Test script to verify lock functionality
set -e

echo "=== Testing Lock Functionality ==="
echo "Running first lock command..."
php bin/console app:create-lock -vvv

echo ""
echo "Running second lock command to test refresh..."
php bin/console app:create-lock -vvv

echo ""
echo "=== Test completed successfully ==="
