// Function to compare similarity between two passwords using substrings
var str = "";
function isPasswordSimilar(newPassword, oldPassword, substringLength) {
    if (substringLength <= 0) throw new Error('Substring length must be a positive integer.');
  
    const newSubstrings = getPasswordSubstrings(newPassword, substringLength);
    const oldSubstrings = getPasswordSubstrings(oldPassword, substringLength);

    // Check if any substring from the new password matches with any substring from the old password
    for (const newSubstring of newSubstrings) {
        if (oldSubstrings.includes(newSubstring)) {
            str = newSubstring;
            return true;
        }
    }
    return false;
}

// Function to generate substrings of a given length from a password
function getPasswordSubstrings(password, length) {
    const substrings = [];
    for (let i = 0; i <= password.length - length; i++) {
        substrings.push(password.substring(i, i + length));
    }
    return substrings;
}

// Example usage
const oldPassword = '12356789';
const newPassword = '123456789';
const substringLength = 5; // Change this to the desired substring length

if (isPasswordSimilar(newPassword, oldPassword, substringLength)) {
    console.log('The new password is too similar to the old password.');
    console.log(str)
} else {
    console.log('The new password is sufficiently different from the old password.');
}
